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
class CreateCrudCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:crud')
            ->setDescription('Create a new crud class.')
            ->addArgument('bundle', InputArgument::OPTIONAL, 'The target bundle')
            ->addArgument('name', InputArgument::OPTIONAL, 'The crud name');
        FileCreator::addInputOptions($this);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundleName, $crudName) = $this->getBundleAndName($input, $output);
        $relativeClass = sprintf('Crud\\%sCrud', $crudName);

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
        $creator->createBundleClass($bundle, $relativeClass, 'Crud.php.twig');
    }

    protected function getBundleAndName(InputInterface $input, OutputInterface $output)
    {
        $bundleName = $input->getArgument('bundle');
        if (!$bundleName) {
            $availableBundles = $this->getContainer()->get('kernel')->getBundles();
            $question = new Question('Target bundle: ');
            $question->setAutocompleterValues(array_keys($availableBundles));
            $bundleName = $this->getHelper('question')->ask($input, $output, $question);
        }

        $crudName = $input->getArgument('name');
        if (!$crudName) {
            $question = new Question('Crud name: (e.g. User) ');
            $crudName = $this->getHelper('question')->ask($input, $output, $question);
            $crudName = ucfirst($crudName);
            if ('crud' === substr(strtolower($crudName), -4)) {
                $crudName = substr($crudName, -4);
            }
        }

        return [
            $bundleName,
            str_replace('/', '\\', $crudName),
        ];
    }
}
