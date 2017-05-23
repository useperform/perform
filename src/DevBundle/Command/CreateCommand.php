<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * CreateCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class CreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->addOption('skip-existing', 's', InputOption::VALUE_NONE, 'Don\'t prompt to overwrite files that already exist.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Always overwrite existing files.');
    }

    protected function get($service)
    {
        return $this->getContainer()->get($service);
    }

    protected function dumpFile(InputInterface $input, OutputInterface $output, $file, $contents)
    {
        return $this->get('perform_dev.file_creator')->create($file, $contents);
    }

    protected function createFile(InputInterface $input, OutputInterface $output, $file, $template, array $vars = [])
    {
        $contents = $this->get('perform_dev.file_creator')->render($template, $vars);

        return $this->dumpFile($input, $output, $file, $contents);
    }

    protected function createBundleClass(InputInterface $input, OutputInterface $output, $bundleName, $relativeClass, $template, array $vars = [])
    {
        $bundle = $this->get('kernel')->getBundle($bundleName);

        return $this->get('perform_dev.file_creator')
            ->createBundleClass($bundle, $relativeClass, $template, $vars);
    }
}
