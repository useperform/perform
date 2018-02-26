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
class CreateInstallerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:installer')
            ->setDescription('Create a new installer class for a bundle.')
            ->addArgument('bundle', InputArgument::OPTIONAL, 'The bundle')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the installer, e.g. Assets or Settings');

        FileCreator::addInputOptions($this);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $bundle = $this->getBundle($input, $output);
        $relativeClass = sprintf('Installer\\%s', $this->getInstallerName($input, $output));

        $vars = [
        ];

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $creator->createBundleClass($bundle, $relativeClass, 'Installer.php.twig', $vars);
    }

    protected function getBundle(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');

        if ($input->getArgument('bundle')) {
            return $kernel->getBundle($input->getArgument('bundle'));
        }

        $q = new Question('Bundle to create the installer in: ');
        $q->setAutocompleterValues(array_keys($kernel->getBundles()));

        return $kernel->getBundle($this->getHelper('question')->ask($input, $output, $q));
    }

    protected function getInstallerName(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('name')) {
            return $this->normaliseName($input->getArgument('name'));
        }

        $q = new Question('Name of the installer, e.g. Assets: ');

        return $this->normaliseName($this->getHelper('question')->ask($input, $output, $q));
    }

    protected function normaliseName($name)
    {
        if (strtolower(substr($name, -9)) !== 'installer') {
            $name .= 'Installer';
        }

        return ucfirst($name);
    }
}
