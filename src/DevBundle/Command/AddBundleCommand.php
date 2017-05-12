<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Perform\DevBundle\File\KernelModifier;
use Symfony\Component\Process\Process;
use Perform\DevBundle\File\RoutingModifier;

/**
 * AddBundleCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AddBundleCommand extends ContainerAwareCommand
{
    protected static $bundles = [
        'PerformNotificationBundle' => ['perform/notification-bundle', [
            'Perform\\NotificationBundle\\PerformNotificationBundle',
        ]],
        'PerformContactBundle' => ['perform/contact-bundle', [
            'Perform\\ContactBundle\\PerformContactBundle',
        ]],
    ];

    protected function configure()
    {
        $this->setName('perform-dev:add-bundle')
            ->setDescription('Add and configure one or many perform bundles.')
            ->addArgument('bundles', InputArgument::IS_ARRAY, 'The bundles to add')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundles = $this->getBundles($input, $output);
        if (empty($bundles)) {
            return;
        }

        $bundleClasses = [];
        foreach ($bundles as $bundle) {
            $bundleClasses = array_merge($bundleClasses, static::$bundles[$bundle][1]);
        }

        $this->addComposerPackages($output, $bundles);

        $k = new KernelModifier($this->getContainer()->get('kernel'));
        try {
            foreach ($bundleClasses as $class) {
                $k->addBundle($class);
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }

        $r = new RoutingModifier($this->getContainer()->get('kernel')->getRootDir().'/config/routing.yml');
        try {
            foreach ($bundles as $bundle) {
                $r->addConfig(RoutingModifier::CONFIGS[$bundle]);
                if ($output->isVerbose()) {
                    $output->writeln(sprintf('Adding %s routes to routing.yml', $bundle));
                }
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }

        $output->writeln(sprintf('Added <info>%s</info>.', implode($bundles, '</info>, <info>')));
    }

    protected function addComposerPackages(OutputInterface $output, array $bundles)
    {
        if (empty($bundles)) {
            return;
        }

        $proc = new Process('composer show --name-only');
        $existingPackages = array_map(function ($bundle) {
            return trim($bundle);
        }, explode(PHP_EOL, $this->getHelper('process')->mustRun($output, $proc)->getOutput()));

        $packageList = '';
        foreach ($bundles as $bundle) {
            $package = static::$bundles[$bundle][0];
            if (in_array($package, $existingPackages)) {
                if ($output->isVeryVerbose()) {
                    $output->writeln(sprintf('Package %s is already installed', $package));
                }

                continue;
            }
            if ($output->isVerbose()) {
                $output->writeln(sprintf('Adding %s to composer.json', $package));
            }

            $packageList .= ' '.$package;
        }

        if (trim($packageList) === '') {
            return;
        }

        $proc = new Process(sprintf('composer require %s dev-master', $packageList));
        $proc->setTty(true);
        $this->getHelper('process')->mustRun($output, $proc);
    }

    protected function getBundles(InputInterface $input, OutputInterface $output)
    {
        $bundles = $input->getArgument('bundles');
        $choices = $this->getUnusedBundles();

        if (empty($choices)) {
            $output->writeln('No unused Perform bundles found.');

            return [];
        }

        if (empty($bundles)) {
            if (!$input->isInteractive()) {
                return [];
            }

            $output->writeln([
                'Select the Perform bundles to add.',
                '',
                'Multiple choices are allowed, separate them with a comma, e.g. 0,2,3.',
            ]);
            $question = new ChoiceQuestion('', array_keys($choices), null);
            $question->setMultiselect(true);
            $bundles = $this->getHelper('question')->ask($input, $output, $question);
        }

        $unknown = array_diff($bundles, array_keys($choices));
        if (!empty($unknown)) {
            $msg = sprintf('Unknown %s "%s"', count($unknown) === 1 ? 'bundle' : 'bundles', implode($unknown, '", "'));
            throw new \Exception($msg);
        }

        return $bundles;
    }

    protected function getUnusedBundles()
    {
        $loadedBundles = array_keys($this->getContainer()->get('kernel')->getBundles());

        return array_filter(static::$bundles, function ($name) use ($loadedBundles) {
            return !in_array($name, $loadedBundles);
        }, ARRAY_FILTER_USE_KEY);
    }
}
