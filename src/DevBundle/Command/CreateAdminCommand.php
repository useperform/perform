<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;

/**
 * CreateAdminCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateAdminCommand extends CreateCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:admin')
            ->setDescription('Create a new admin class for an entity.')
            ->addArgument('entity', InputArgument::OPTIONAL, 'The entity name');
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getArgument('entity');
        $em = $this->get('doctrine.orm.entity_manager');

        $mappings = array_map(function ($meta) {
            return $meta->getName();
        }, $em->getMetadataFactory()->getAllMetadata());

        $mapper = function ($class, $classBasename, $bundleName, $bundleClass) use ($mappings) {
            if (!in_array($class, $mappings)) {
                // not a doctrine entity
                return false;
            }

            return [
                $bundleName, $classBasename,
            ];
        };
        //mapper function results indexed by class
        $entities = $this->get('perform_base.bundle_searcher')
                  ->findItemsInNamespaceSegment('Entity', $mapper);

        //add index by alias, and create autocomplete choices with aliases only
        $choices = [];
        foreach ($entities as $class => $item) {
            $alias = $item[0].':'.$item[1];
            $entities[$alias] = $item;
            $choices[$alias] = $item;
        }

        if (!$entity) {
            $question = new Question('Entity name: (e.g. AppBundle:Item) ', array_keys($choices));
            $question->setAutocompleterValues(array_keys($choices));
            $entity = $this->getHelper('question')->ask($input, $output, $question);
        }

        $entity = str_replace('/', '\\', $entity);

        if (!isset($entities[$entity])) {
            throw new \Exception(sprintf('Unknown entity "%s"', $entity));
        }

        $bundleName = $entities[$entity][0];
        $relativeClass = sprintf('Admin\\%sAdmin', $entities[$entity][1]);
        $this->createBundleClass($input, $output, $bundleName, $relativeClass, 'Admin.php.twig');
    }
}
