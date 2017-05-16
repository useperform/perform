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
use Perform\DevBundle\File\YamlModifier;

/**
 * AddBundleCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AddBundleCommand extends ContainerAwareCommand
{
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

        $this->addComposerPackages($output, $bundles);
        $this->addBundleClasses($output, $bundles);
        $this->addRoutes($output, $bundles);
        $this->addConfigs($output, $bundles);

        foreach ($bundles as $bundle) {
            $output->writeln(sprintf('Added <info>%s</info>.', $bundle->getBundleName()));
        }
    }

    protected function addBundleClasses(OutputInterface $output, array $bundles)
    {
        $bundleClasses = [];
        foreach ($bundles as $bundle) {
            $bundleClasses = array_merge($bundleClasses, [$bundle->getBundleClass()], $bundle->getRequiredBundleClasses());
        }

        $k = new KernelModifier($this->getContainer()->get('kernel'));
        try {
            foreach ($bundleClasses as $class) {
                $k->addBundle($class);
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    protected function addRoutes(OutputInterface $output, array $bundles)
    {
        $r = new YamlModifier($this->getContainer()->get('kernel')->getRootDir().'/config/routing.yml');
        try {
            foreach ($bundles as $resource) {
                $r->addConfig($resource->getRoutes());
                if ($output->isVerbose()) {
                    $output->writeln(sprintf('Adding %s routes to routing.yml', $resource->getBundleName()));
                }
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    protected function addConfigs(OutputInterface $output, array $bundles)
    {
        $r = new YamlModifier($this->getContainer()->get('kernel')->getRootDir().'/config/config.yml');
        try {
            foreach ($bundles as $resource) {
                $config = $resource->getConfig();
                if (!$config) {
                    continue;
                }

                //only check for the first line of the config entry, e.g. perform_contact:
                $firstLine = trim(explode(PHP_EOL, trim($config))[0]);
                $checkPattern = sprintf('/^%s/m', $firstLine);
                $r->addConfig($config, $checkPattern);
                if ($output->isVerbose()) {
                    $output->writeln(sprintf('Adding default %s config to config.yml', $resource->getBundleName()));
                }
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    protected function addComposerPackages(OutputInterface $output, array $bundles)
    {
        $proc = new Process('composer show --name-only');
        $existingPackages = array_map(function ($bundle) {
            return trim($bundle);
        }, explode(PHP_EOL, $this->getHelper('process')->mustRun($output, $proc)->getOutput()));

        $packageList = '';
        foreach ($bundles as $bundle) {
            $package = $bundle->getComposerPackage();
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
        $bundleNames = $input->getArgument('bundles');
        $unusedBundles = $this->getUnusedBundles();

        if (empty($unusedBundles)) {
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
            $question = new ChoiceQuestion('', array_keys($unusedBundles), null);
            $question->setMultiselect(true);
            $bundleNames = $this->getHelper('question')->ask($input, $output, $question);
        }

        $unknown = array_diff($bundleNames, array_keys($unusedBundles));
        if (!empty($unknown)) {
            $msg = sprintf('Unknown %s "%s"', count($unknown) === 1 ? 'bundle' : 'bundles', implode($unknown, '", "'));
            throw new \Exception($msg);
        }

        $bundles = array_intersect_key($unusedBundles, array_flip($bundleNames));

        return $bundles;
    }

    protected function getUnusedBundles()
    {
        $possibleBundles = $this->getContainer()->get('perform_dev.resource_registry')->getParentResources();
        $loadedBundles = array_keys($this->getContainer()->get('kernel')->getBundles());

        $unusedBundles = [];
        foreach ($possibleBundles as $resource) {
            if (in_array($resource->getBundleName(), $loadedBundles)) {
                continue;
            }

            $unusedBundles[$resource->getBundleName()] = $resource;
        }

        return $unusedBundles;
    }
}
