<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Perform\BaseBundle\Util\BundleSearcher;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Perform\BaseBundle\DataFixtures\ORM\Purger;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * FixturesRunCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FixturesRunCommand extends ContainerAwareCommand
{
    protected $excludedEntities = [];

    protected function configure()
    {
        $this->setName('perform:fixtures')
            ->setDescription('Run database fixtures')
            ->addOption(
                'only-bundles',
                'o',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Only run fixtures for the given bundles'
            )->addOption(
                'exclude-bundles',
                'x',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Exclude the given bundles'
            )->addOption(
                'append',
                '',
                InputOption::VALUE_NONE,
                'Don\'t empty database tables'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        if ($input->isInteractive() && !$input->getOption('append')) {
            if (!$this->askConfirmation($input, $output, '<question>Some database tables will be emptied. Are you sure y/n ?</question>', false)) {
                return;
            }
        }

        $this->excludedEntities = array_keys(
            $this->getContainer()->getParameter('perform_base.extended_entities'));

        $fixtures = $this->getFixtures($this->getInputBundles($input), $output);
        if (empty($fixtures)) {
            $output->writeln('No fixtures found.');
            return;
        }
        $purger = new Purger($em, $this->getDeclaredClasses($input, $fixtures));
        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures, $input->getOption('append'), false);

        $count = count($fixtures);
        $inflection = $count === 1 ? 'fixture' : 'fixtures';
        $output->writeln(sprintf('Ran <info>%s</info> %s.', $count, $inflection));
    }

    protected function getFixtures(array $bundleNames, OutputInterface $output)
    {
        if (empty($bundleNames)) {
            return [];
        }

        $mapper = function ($class) use ($output) {
            $r = new \ReflectionClass($class);
            if (!$r->isSubclassOf(EntityDeclaringFixtureInterface::class) || $r->isAbstract()) {
                return false;
            }
            $fixture = $r->newInstance();
            $usedExcludedEntities = array_intersect($fixture->getEntityClasses(), $this->excludedEntities);
            if (count($usedExcludedEntities) > 0) {
                $output->writeln(sprintf(
                    'Skipping <info>%s</info> because <info>%s</info> has been extended',
                    get_class($fixture),
                    $usedExcludedEntities[0]));

                return false;
            }

            if ($fixture instanceof ContainerAwareInterface) {
                $fixture->setContainer($this->getContainer());
            }

            return $fixture;
        };

        $searcher = new BundleSearcher($this->getContainer());
        $fixtures = $searcher->findItemsInNamespaceSegment('DataFixtures\\ORM', $mapper, $bundleNames);

        return $fixtures;
    }

    /**
     * Get an array of declared entity classes from a set of fixtures, but only
     * if --only-bundles or --exclude-bundles has been set. Otherwise, return an
     * empty array.
     */
    protected function getDeclaredClasses(InputInterface $input, array $fixtures)
    {
        if (!$input->getOption('only-bundles') && !$input->getOption('exclude-bundles')) {
            return [];
        }

        $declaredClasses = [];
        foreach ($fixtures as $fixture) {
            $declaredClasses = array_merge($declaredClasses, $fixture->getEntityClasses());
        }

        return $declaredClasses;
    }

    /**
     * Get bundle names from the given input.
     * --only-bundles takes priority over --exclude-bundles.
     * Defaults to all bundles.
     *
     * @param InputInterface $input
     */
    protected function getInputBundles(InputInterface $input)
    {
        $bundles = $this->getApplication()->getKernel()->getBundles();

        if ($input->getOption('only-bundles')) {
            $included = $this->normaliseBundleNames($input->getOption('only-bundles'));

            $bundles = array_filter($bundles, function ($bundle) use ($included) {
                foreach ($included as $name) {
                    if ($name === strtolower($bundle->getName())) {
                        return true;
                    }
                }
                return false;
            });

        } else if ($input->getOption('exclude-bundles')) {
            $excluded = $this->normaliseBundleNames($input->getOption('exclude-bundles'));

            $bundles = array_filter($bundles, function ($bundle) use ($excluded) {
                foreach ($excluded as $name) {
                    if ($name === strtolower($bundle->getName())) {
                        return false;
                    }
                }
                return true;
            });
        }

        return array_map(function ($bundle) {
            return $bundle->getName();
        }, $bundles);
    }

    protected function normaliseBundleNames(array $bundleNames)
    {
        return array_map(function($name) {
            $name = strtolower($name);
            if (substr($name, -6) !== 'bundle') {
                $name .= 'bundle';
            }

            return $name;
        }, $bundleNames);
    }

    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);

        return $questionHelper->ask($input, $output, $question);
    }
}
