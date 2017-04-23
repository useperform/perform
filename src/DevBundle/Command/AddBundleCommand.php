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

        $packageList = '';
        $bundleClasses = [];
        foreach ($bundles as $bundle) {
            $packageList .= ' '.static::$bundles[$bundle][0];
            $bundleClasses = array_merge($bundleClasses, static::$bundles[$bundle][1]);
        }

        $cmd = sprintf('composer require %s dev-master', $packageList);
        $proc = new Process($cmd);
        $proc->setTty(true);
        $this->getHelper('process')->mustRun($output, $proc);

        $k = new KernelModifier($this->getContainer()->get('kernel'));
        try {
            foreach ($bundleClasses as $class) {
                $k->addBundle($class);
            }
        } catch (\Exception $e) {
            $output->writeln((string) $e);
            $output->writeln(sprintf('Unable to add "%s" to your AppKernel. Please add it manually.', $choices[$bundle][1]));
        }

        //add default config of the bundles to config.yml
    }

    protected function getBundles(InputInterface $input, OutputInterface $output)
    {
        $bundles = $input->getArgument('bundles');
        $choices = $this->getUnusedBundles();

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
