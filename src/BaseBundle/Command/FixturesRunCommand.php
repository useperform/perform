<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Perform\BaseBundle\DataFixtures\ORM\Purger;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

/**
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
                'append',
                '',
                InputOption::VALUE_NONE,
                "Don't empty database tables"
            );
        BundleFilter::addOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $this->excludedEntities = array_keys(
            $this->getContainer()->getParameter('perform_base.extended_entities'));

        $fixtures = $this->getFixtures($input, $output);
        if (empty($fixtures)) {
            $output->writeln('No fixtures found.');

            return;
        }
        if ($input->isInteractive() && !$input->getOption('append')) {
            if (!$this->askConfirmation($input, $output, '<question>Some database tables will be emptied. Are you sure y/n ?</question>', false)) {
                return;
            }
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

    protected function getFixtures(InputInterface $input, OutputInterface $output)
    {
        $usedBundleNames = BundleFilter::filterBundleNames($input, $this->getApplication()->getKernel()->getBundles());

        $mapper = function ($class) use ($output) {
            $r = new \ReflectionClass($class);
            if (!$r->isSubclassOf(FixtureInterface::class) || $r->isAbstract()) {
                return false;
            }
            if ($this->getContainer()->has($class)) {
                return $this->getContainer()->get($class);
            }
            $fixture = $r->newInstance();
            $usedExcludedEntities = $r->isSubclassOf(EntityDeclaringFixtureInterface::class) ?
                                  array_values(array_intersect($fixture->getEntityClasses(), $this->excludedEntities)) : [];
            if (count($usedExcludedEntities) > 0) {
                if ($output->isVerbose()) {
                    $output->writeln(sprintf(
                        'Skipping <info>%s</info> because <info>%s</info> has been extended',
                        get_class($fixture),
                        $usedExcludedEntities[0]));
                }

                return false;
            }

            if ($fixture instanceof ContainerAwareInterface) {
                $fixture->setContainer($this->getContainer());
            }

            return $fixture;
        };

        $fixtures = $this->getContainer()->get('perform_base.bundle_searcher')
                  ->findClassesWithNamespaceSegment('DataFixtures\\ORM', $mapper, $usedBundleNames);

        usort($fixtures, function ($a, $b) {
            $aOrder = $a instanceof OrderedFixtureInterface ? $a->getOrder() : 0;
            $bOrder = $b instanceof OrderedFixtureInterface ? $b->getOrder() : 0;

            if ($aOrder === $bOrder) {
                return 0;
            }

            return $aOrder < $bOrder ? -1 : 1;
        });

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
            if (!$fixture instanceof EntityDeclaringFixtureInterface) {
                throw new \RuntimeException(sprintf('%s does not implement %s. To be able to use the --only-bundles and --exclude-bundles options, all of the target fixture classes must implement this interface.', get_class($fixture), EntityDeclaringFixtureInterface::class));
            }

            $declaredClasses = array_merge($declaredClasses, $fixture->getEntityClasses());
        }

        return $declaredClasses;
    }

    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);

        return $questionHelper->ask($input, $output, $question);
    }
}
