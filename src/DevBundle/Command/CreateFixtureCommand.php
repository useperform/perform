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
class CreateFixtureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:fixture')
            ->setDescription('Create a new fixture class for an entity.')
            ->addArgument('entity', InputArgument::OPTIONAL, 'The entity name');

        FileCreator::addInputOptions($this);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundleName, $entityName, $entityClass) = $this->getEntity($input, $output);
        $relativeClass = sprintf('DataFixtures\\ORM\Load%sData', $entityName);

        $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);

        $vars = [
            'entityClass' => $entityClass,
            'entityName' => $entityName,
        ];

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $creator->createBundleClass($bundle, $relativeClass, 'DataFixture.php.twig', $vars);
    }

    protected function getEntity(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getArgument('entity');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $mappings = array_map(function ($meta) {
            return $meta->getName();
        }, $em->getMetadataFactory()->getAllMetadata());

        $mapper = function ($class, $classBasename, $bundle) use ($mappings) {
            if (!in_array($class, $mappings)) {
                // not a doctrine entity
                return false;
            }

            return [
                $bundle->getName(), $classBasename, $class,
            ];
        };
        //mapper function results indexed by class
        $entities = $this->getContainer()->get('perform_base.bundle_searcher')
                  ->findClassesWithNamespaceSegment('Entity', $mapper);

        //add index by alias, and create autocomplete choices with aliases only
        $choices = [];
        foreach ($entities as $class => $item) {
            $alias = $item[0].':'.$item[1];
            $entities[$alias] = $item;
            $choices[$alias] = $item;
        }

        if (!$entity) {
            $question = new Question('Entity name: (e.g. AppBundle:Item) ');
            $question->setAutocompleterValues(array_keys($choices));
            $entity = $this->getHelper('question')->ask($input, $output, $question);
        }

        $entity = str_replace('/', '\\', $entity);

        if (!isset($entities[$entity])) {
            throw new \Exception(sprintf('Unknown entity "%s"', $entity));
        }

        return $entities[$entity];
    }
}
