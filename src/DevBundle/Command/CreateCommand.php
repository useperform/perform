<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\DevBundle\File\FileCreator;

/**
 * CreateCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class CreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        FileCreator::addInputOptions($this);
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
