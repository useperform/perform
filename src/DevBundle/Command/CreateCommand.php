<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Perform\DevBundle\Exception\FileException;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
        $creator = $this->get('perform_dev.file_creator');
        $msg = sprintf('Created <info>%s</info>', $file);

        try {
            $creator->create($file, $contents);
            $output->writeln($msg);

            return true;
        } catch (FileException $e) {
            if ($input->getOption('skip-existing')) {
                return;
            }

            if (!$input->getOption('force')) {
                $question = new ConfirmationQuestion("<info>$file</info> exists. Overwrite? (y/N) ", false);
                //add another option - view a diff by creating a temp file and comparing
                if (!$this->getHelper('question')->ask($input, $output, $question)) {
                    return;
                }
            }

            $creator->forceCreate($file, $contents);
            $output->writeln($msg);

            return true;
        }
    }

    protected function createFile(InputInterface $input, OutputInterface $output, $file, $template, array $vars = [])
    {
        $contents = $this->get('perform_dev.file_creator')->render($template, $vars);

        return $this->dumpFile($input, $output, $file, $contents);
    }

    protected function createBundleClass(InputInterface $input, OutputInterface $output, $bundleName, $relativeClass, $template, array $vars = [])
    {
        $bundle = $this->get('kernel')->getBundle($bundleName);
        list($file, $vars) = $this->get('perform_dev.file_creator')->resolveBundleClass($bundle, $relativeClass, $vars);

        return $this->createFile($input, $output, $file, $template, $vars);
    }
}
