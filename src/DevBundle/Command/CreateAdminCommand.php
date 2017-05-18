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
        list($bundleName, $entityName) = $this->getEntity($input, $output);
        $relativeClass = sprintf('Admin\\%sAdmin', $entityName);

        $this->createBundleClass($input, $output, $bundleName, $relativeClass, 'Admin.php.twig');

        $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
        $this->addService($output, $bundle, $entityName, $bundle->getNamespace().'\\'.$relativeClass);
    }

    protected function getEntity(InputInterface $input, OutputInterface $output)
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

        return $entities[$entity];
    }

    protected function addService(OutputInterface $output, BundleInterface $bundle, $entityName, $adminClass)
    {
        $basename = preg_replace('/Bundle$/', '', $bundle->getName());
        $service = sprintf('%s.admin.%s', Container::underscore($basename), Container::underscore($entityName));
        $entity = $bundle->getName().':'.$entityName;

        $yaml = $this->buildServiceYaml($service, $adminClass, $entity);
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
        //only check for the service, e.g. app.admin.item:
        $checkPattern = sprintf('/ +%s:/m', $service);
        $c->addConfig($yaml, $checkPattern);

        $output->writeln(sprintf('Added service definition <info>%s</info> to <info>%s</info>', $service, $file));
    }

    protected function buildServiceYaml($service, $adminClass, $entity)
    {
        $tmpl = '
    %s:
        class: %s
        tags:
            - {name: perform_base.admin, entity: "%s"}
';

        return sprintf($tmpl, $service, $adminClass, $entity);
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
