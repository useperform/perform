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

        $this->addService($output, $bundle, $bundle->getNamespace().'\\'.$relativeClass, $crudName);
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
            if (substr(strtolower($crudName), -4) === 'crud') {
                $crudName = substr($crudName, -4);
            }
        }

        return [
            $bundleName,
            str_replace('/', '\\', $crudName),
        ];
    }

    protected function addService(OutputInterface $output, BundleInterface $bundle, $crudClass, $crudName)
    {
        $basename = preg_replace('/Bundle$/', '', $bundle->getName());
        $service = sprintf('%s.crud.%s', Container::underscore($basename), Container::underscore($crudName));

        $yaml = $this->buildServiceYaml($service, $crudClass, $crudName);
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

    protected function buildServiceYaml($service, $crudClass, $crudName)
    {
        $tmpl = '
    %s:
        class: %s
        tags:
            - {name: perform_base.crud, crud_name: "%s"}
';

        return sprintf($tmpl, $service, $crudClass, Container::underscore($crudName));
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
