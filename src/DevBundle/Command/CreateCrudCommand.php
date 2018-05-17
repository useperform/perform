<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Perform\DevBundle\File\YamlModifier;
use Symfony\Component\Finder\Finder;
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
            ->setDescription('Create a new crud class for an entity.')
            ->addArgument('entity', InputArgument::OPTIONAL, 'The entity name');
        FileCreator::addInputOptions($this);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundleName, $entityName) = $this->getEntity($input, $output);
        $relativeClass = sprintf('Crud\\%sCrud', $entityName);

        $vars = $this->getTwigVars($input, $output, $bundleName, $entityName);

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
        $creator->createBundleClass($bundle, $relativeClass, 'Crud.php.twig', $vars);

        $this->addService($output, $bundle, $entityName, $bundle->getNamespace().'\\'.$relativeClass);
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
                $bundle->getName(), $classBasename,
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

    protected function getTwigVars(InputInterface $input, OutputInterface $output, $bundleName, $entityName)
    {
        $basename = preg_replace('/Bundle$/', '', $bundleName);
        $default = sprintf('%s_crud_%s_', Container::underscore($basename), Container::underscore($entityName));
        $question = new Question(sprintf('Route prefix (%s): ', $default), $default);
        $routePrefix = $this->getHelper('question')->ask($input, $output, $question);

        return [
            'routePrefix' => $routePrefix,
        ];
    }

    protected function addService(OutputInterface $output, BundleInterface $bundle, $entityName, $crudClass)
    {
        $basename = preg_replace('/Bundle$/', '', $bundle->getName());
        $service = sprintf('%s.crud.%s', Container::underscore($basename), Container::underscore($entityName));
        $entity = $bundle->getName().':'.$entityName;

        $yaml = $this->buildServiceYaml($service, $crudClass, $entity);
        $file = $this->getServiceFile($bundle);

        if (!$file) {
            $output->writeln([
                '',
                '<error>Warning</error> Unable to find a file to insert a service definition.',
                '',
                'Add the following to your services file:',
                '',
                $yaml,
            ]);

            return;
        }

        $c = new YamlModifier($file->getPathname());
        //only check for the service, e.g. app.crud.item:
        $checkPattern = sprintf('/ +%s:/m', $service);
        $c->addConfig($yaml, $checkPattern);

        $output->writeln(sprintf('Added service definition <info>%s</info> to <info>%s</info>', $service, $file));
    }

    protected function buildServiceYaml($service, $crudClass, $entity)
    {
        $tmpl = '
    %s:
        class: %s
        tags:
            - {name: perform_base.crud, entity: "%s"}
';

        return sprintf($tmpl, $service, $crudClass, $entity);
    }

    protected function getServiceFile(BundleInterface $bundle)
    {
        return Finder::create()
            ->files()
            ->in($bundle->getPath().'/Resources/config')
            ->in($this->getContainer()->get('kernel')->getRootDir())
            ->name('services.yml')
            ->getIterator()
            ->current();
    }
}
