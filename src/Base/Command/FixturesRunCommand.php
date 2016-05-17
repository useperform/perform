<?php

namespace Admin\Base\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * FixturesRunCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FixturesRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('admin:base:fixtures_run')
            ->setDescription('Run database fixtures')
            ->addOption(
            'bundles',
            'b',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Only run fixtures for the given bundles'
        )->addOption(
            'exclude-bundles',
            'x',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Exclude fixtures in the given bundles'
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
            if (!$this->askConfirmation($input, $output, '<question>Database will be emptied. Are you sure y/n ?</question>', false)) {
                return;
            }
        }

        $fixtures = [];
        foreach ($this->getInputBundles($input) as $bundle) {
            $fixtures = array_merge($fixtures, $this->getBundleFixtures($bundle));
        }

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures, $input->getOption('append'), false);

        $count = count($fixtures);
        $inflection = $count === 1 ? 'fixture' : 'fixtures';
        $output->writeln(sprintf('Ran <info>%s</info> %s.', $count, $inflection));
    }

    protected function getBundleFixtures(BundleInterface $bundle)
    {
        $namespace = $bundle->getNamespace().'\\DataFixtures\\ORM\\';
        $directory = $bundle->getPath().'/DataFixtures/ORM';
        if (!is_dir($directory)) {
            return [];
        }

        $fixtures = [];
        $files = new \DirectoryIterator($directory);
        foreach ($files as $file) {
            if (!$file->isFile() || substr($file->getFilename(), -4) !== '.php') {
                continue;
            }
            $class = $namespace.$file->getBasename('.php');
            $r = new \ReflectionClass($class);
            if (!$r->isSubclassOf('Doctrine\Common\DataFixtures\FixtureInterface') || $r->isAbstract()) {
                continue;
            }
            $fixtures[] = $r->newInstance();
        }

        return $fixtures;
    }

    /**
     * Get bundles from the given input.
     * --bundles takes priority over --exclude-bundles.
     * Defaults to all bundles.
     *
     * @param InputInterface $input
     */
    protected function getInputBundles(InputInterface $input)
    {
        if ($input->getOption('bundles')) {
            return array_map(function ($bundleName) {
                return $this->getApplication()->getKernel()->getBundle($bundleName);
            }, $input->getOption('bundles'));
        }

        if ($input->getOption('exclude-bundles')) {
            $excludedBundles = $input->getOption('exclude-bundles');

            return array_filter($this->getApplication()->getKernel()->getBundles(), function ($bundle) use ($excludedBundles) {
                return !in_array($bundle->getName(), $excludedBundles);
            });
        }

        return $this->getApplication()->getKernel()->getBundles();
    }

    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);

        return $questionHelper->ask($input, $output, $question);
    }
}
