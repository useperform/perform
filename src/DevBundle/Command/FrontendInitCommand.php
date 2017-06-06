<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\DevBundle\File\FileCreator;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

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
            ->addArgument('bundle', InputArgument::OPTIONAL, 'The bundle to create the files in')
            ->addOption('frontend', 'f', InputOption::VALUE_REQUIRED, 'The frontend to use');

        FileCreator::addInputOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundle = $this->getBundle($input, $output);
        $frontend = $this->getFrontend($input, $output);

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $frontend->createBaseFiles($bundle, $creator);
    }

    protected function getBundle(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');

        if ($input->getArgument('bundle')) {
            return $kernel->getBundle($input->getArgument('bundle'));
        }

        $q = new Question('Bundle to create the frontend in: ');
        $q->setAutocompleterValues(array_keys($kernel->getBundles()));

        return $kernel->getBundle($this->getHelper('question')->ask($input, $output, $q));
    }

    protected function getFrontend(InputInterface $input, OutputInterface $output)
    {
        $registry = $this->getContainer()->get('perform_dev.frontend_registry');

        if ($input->getOption('frontend')) {
            return $registry->get($input->getOption('frontend'));
        }

        $choices = array_keys($registry->all());
        $q = new ChoiceQuestion('Select the frontend to use: ', $choices);

        return $registry->get($this->getHelper('question')->ask($input, $output, $q));
    }
}
