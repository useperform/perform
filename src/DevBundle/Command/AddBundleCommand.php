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
        $parents = $this->getParentResources($input, $output);
        if (empty($parents)) {
            return;
        }

        $this->addComposerPackages($input, $output, $parents);

        $resolved = $this->getContainer()->get('perform_dev.resource_registry')
                  ->resolveResources(array_keys($parents));

        $this->addBundleClasses($output, $resolved);
        $this->addRoutes($output, $resolved);
        $this->addConfigs($output, $resolved);

        foreach ($parents as $resource) {
            $output->writeln(sprintf('Added <info>%s</info>.', $resource->getBundleName()));
        }
    }

    protected function addBundleClasses(OutputInterface $output, array $resources)
    {
        $k = new KernelModifier($this->getContainer()->get('kernel'));
        try {
            foreach ($resources as $resource) {
                $k->addBundle($resource->getBundleClass());
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    protected function addRoutes(OutputInterface $output, array $resources)
    {
        $r = new YamlModifier($this->getContainer()->get('kernel')->getRootDir().'/config/routing.yml');
        try {
            foreach ($resources as $resource) {
                $routes = $resource->getRoutes();
                if (!$routes) {
                    continue;
                }

                $r->addConfig($routes);
                if ($output->isVerbose()) {
                    $output->writeln(sprintf('Adding %s routes to routing.yml', $resource->getBundleName()));
                }
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    protected function addConfigs(OutputInterface $output, array $resources)
    {
        $r = new YamlModifier($this->getContainer()->get('kernel')->getRootDir().'/config/config.yml');
        try {
            foreach ($resources as $resource) {
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

    /**
     * @param InputInterface           $input
     * @param OutputInterface           $output
     * @param ParentResourceInterface[] $resources
     */
    protected function addComposerPackages(InputInterface $input, OutputInterface $output, array $resources)
    {
        $proc = new Process('composer show --name-only');
        $existingPackages = array_map(function ($pkg) {
            return trim($pkg);
        }, explode(PHP_EOL, $this->getHelper('process')->mustRun($output, $proc)->getOutput()));

        $packageList = '';
        $optionalPackages = [];
        foreach ($resources as $resource) {
            foreach ($resource->getOptionalComposerPackages() as $package => $message) {
                if (in_array($package, $existingPackages)) {
                    continue;
                }

                if (!isset($optionalPackages[$package])) {
                    $optionalPackages[$package] = [];
                }
                $optionalPackages[$package][] = sprintf('<comment>(%s)</comment> %s', $resource->getBundleName(), $message);
            }

            $package = $resource->getComposerPackage();
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

        $extraPackages = $this->addOptionalComposerPackages($input, $output, $optionalPackages);

        if (trim($packageList) === '') {
            if (!empty($extraPackages)) {
                $proc = new Process('composer update');
                $proc->setTty(true);
                $this->getHelper('process')->mustRun($output, $proc);
            }

            return;
        }

        $proc = new Process(sprintf('composer require %s dev-master', $packageList));
        $proc->setTty(true);
        $this->getHelper('process')->mustRun($output, $proc);
    }

    protected function addOptionalComposerPackages(InputInterface $input, OutputInterface $output, array $optionalPackages)
    {
        if (empty($optionalPackages)) {
            return;
        }

        $messages = [
            '',
            'Use any suggested composer packages?',
            '',
        ];
        foreach ($optionalPackages as $pkg => $reasons) {
            $messages[] = sprintf('<info>%s</info>', $pkg);
            foreach ($reasons as $reason) {
                $messages[] = '    '.$reason;
            }
        }

        $messages[] = '';
        $messages[] = 'Multiple choices are allowed, separate them with a comma, e.g. 0,2,3.';
        $messages[] = '';
        $messages[] = '(Or leave blank to skip)';
        $messages[] = '';

        $question = new ChoiceQuestion($messages, array_keys($optionalPackages));
        $question->setMultiselect(true);

        //accept an empty string to make the choice question skippable
        $defaultValidator = $question->getValidator();
        $validator = function ($selected) use ($defaultValidator) {
            if (trim($selected) === '') {
                return [];
            }

            return $defaultValidator($selected);
        };

        $question->setValidator($validator);
        $chosen = $this->getHelper('question')->ask($input, $output, $question);

        if (empty($chosen)) {
            return [];
        }
        $packageList = implode(' ', $chosen);

        $proc = new Process(sprintf('composer require %s --no-update', $packageList));
        $proc->setTty(true);
        $this->getHelper('process')->mustRun($output, $proc);

        return $chosen;
    }

    protected function getParentResources(InputInterface $input, OutputInterface $output)
    {
        $unused = $this->getUnusedParentResources();

        if (empty($unused)) {
            $output->writeln('No unused Perform bundles found.');

            return [];
        }

        $bundleNames = $input->getArgument('bundles');
        if (empty($bundleNames)) {
            if (!$input->isInteractive()) {
                return [];
            }

            $output->writeln([
                'Select the Perform bundles to add.',
                '',
                'Multiple choices are allowed, separate them with a comma, e.g. 0,2,3.',
            ]);
            $question = new ChoiceQuestion('', array_keys($unused), null);
            $question->setMultiselect(true);
            $bundleNames = $this->getHelper('question')->ask($input, $output, $question);
        }

        $unknown = array_diff($bundleNames, array_keys($unused));
        if (!empty($unknown)) {
            $msg = sprintf('Unknown %s "%s"', count($unknown) === 1 ? 'bundle' : 'bundles', implode($unknown, '", "'));
            throw new \Exception($msg);
        }

        return array_intersect_key($unused, array_flip($bundleNames));
    }

    protected function getUnusedParentResources()
    {
        $possibleBundles = $this->getContainer()->get('perform_dev.resource_registry')
                         ->getParentResources();
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
