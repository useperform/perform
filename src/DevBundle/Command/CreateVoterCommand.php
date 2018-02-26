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
class CreateVoterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:voter')
            ->setDescription('Create a new security voter.')
            ->addArgument('bundle', InputArgument::OPTIONAL, 'The bundle')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the voter, e.g. User');

        FileCreator::addInputOptions($this);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $bundle = $this->getBundle($input, $output);
        $relativeClass = sprintf('Security\\Voter\\%s', $this->getVoterName($input, $output));

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $creator->createBundleClass($bundle, $relativeClass, 'Voter.php.twig');
    }

    protected function getBundle(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');

        if ($input->getArgument('bundle')) {
            return $kernel->getBundle($input->getArgument('bundle'));
        }

        $q = new Question('Bundle to create the voter in: ');
        $q->setAutocompleterValues(array_keys($kernel->getBundles()));

        return $kernel->getBundle($this->getHelper('question')->ask($input, $output, $q));
    }

    protected function getVoterName(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('name')) {
            return $this->normaliseName($input->getArgument('name'));
        }

        $q = new Question('Name of the voter, e.g. User: ');

        return $this->normaliseName($this->getHelper('question')->ask($input, $output, $q));
    }

    protected function normaliseName($name)
    {
        if (strtolower(substr($name, -5)) !== 'voter') {
            $name .= 'Voter';
        }

        return ucfirst($name);
    }
}
