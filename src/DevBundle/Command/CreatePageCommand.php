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
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreatePageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:page')
            ->setDescription('Create a frontend page in a bundle.')
            ->addArgument('page', InputArgument::OPTIONAL, 'The namespaced page, e.g. AppBundle:Products:index or AppBundle:home')
            ->addOption('frontend', 'f', InputOption::VALUE_REQUIRED, 'The frontend to use');

        FileCreator::addInputOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundle, $page) = $this->getPage($input, $output);
        $page = str_replace(':', '/', $page);
        $page = preg_replace('/.html.twig$|.twig$|.html$/', '', $page);

        $frontend = $this->getFrontend($input, $output);

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $frontend->createPage($bundle, $creator, $page);
    }

    protected function getPage(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');

        if ($input->getArgument('page')) {
            $full = $input->getArgument('page');
            $pieces = explode(':', $full, 2);
            if (count($pieces) !== 2) {
                throw new \RuntimeException('Page name must contain the bundle name, e.g. AppBundle:Products:index or AppBundle:home');
            }
            $bundle = $kernel->getBundle($pieces[0]);

            return [$bundle, $pieces[1]];
        }

        $q = new Question('Bundle to create the page in: ');
        $q->setAutocompleterValues(array_keys($kernel->getBundles()));
        $bundle = $kernel->getBundle($this->getHelper('question')->ask($input, $output, $q));

        $q = new Question('Name of the page, e.g. <info>Products:index</info> or <info>home</info>: ');
        $page = $this->getHelper('question')->ask($input, $output, $q);

        return [$bundle, $page];
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
