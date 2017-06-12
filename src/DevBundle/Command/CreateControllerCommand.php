<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\DevBundle\File\FileCreator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateControllerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:controller')
            ->setDescription('Create a new controller class in a bundle.')
            ->addArgument('controller', InputArgument::OPTIONAL, 'The controller name, e.g. AppBundle:Page');

        FileCreator::addInputOptions($this);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundle, $controller) = $this->getController($input, $output);
        $relativeClass = sprintf('Controller\\%sController',
                                 ucfirst(preg_replace('/Controller$|controller$/', '', $controller)));
        $vars = [
            'base_url' => '/'.strtolower($controller),
        ];

        $this->getContainer()
            ->get('perform_dev.file_creator')
            ->createBundleClass($bundle, $relativeClass, 'Controller.php.twig', $vars);
    }

    protected function getController(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');

        if ($input->getArgument('controller')) {
            $pieces = explode(':', $input->getArgument('controller'), 2);
            if (count($pieces) !== 2) {
                throw new \RuntimeException('Controller name must be of the form Bundle:Name, e.g. AppBundle:Products');
            }
            $bundle = $kernel->getBundle($pieces[0]);

            return [$bundle, $pieces[1]];
        }

        $q = new Question('Bundle to create the controller in: ');
        $q->setAutocompleterValues(array_keys($kernel->getBundles()));
        $bundle = $kernel->getBundle($this->getHelper('question')->ask($input, $output, $q));

        $q = new Question('Name of the controller class, e.g. <info>Products</info>: ');
        $controller = $this->getHelper('question')->ask($input, $output, $q);

        return [$bundle, $controller];
    }
}
