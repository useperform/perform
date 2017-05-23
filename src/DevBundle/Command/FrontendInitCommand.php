<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\DevBundle\File\FileCreator;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * FrontendInitCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FrontendInitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:frontend:init')
            ->setDescription('Create the base files for frontend pages in a bundle.')
            ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle to create the files in')
            ->addOption('frontend', 'f', InputOption::VALUE_REQUIRED, 'The frontend to use');

        FileCreator::addInputOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $frontend = $this->getContainer()->get('perform_dev.frontend_registry')
                  ->get($this->getFrontend($input, $output));
        $bundle = $this->getContainer()->get('kernel')->getBundle($input->getArgument('bundle'));

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $frontend->createBaseFiles($bundle, $creator);
    }

    protected function getFrontend(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('frontend')) {
            return $input->getOption('frontend');
        }

        $choices = array_keys($this->getContainer()->get('perform_dev.frontend_registry')->all());
        $q = new ChoiceQuestion('Select the frontend to use: ', $choices);

        return $this->getHelper('question')->ask($input, $output, $q);
    }
}
