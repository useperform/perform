<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Doctrine\DBAL\Types\Type;

/**
 * CreateEntityCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateEntityCommand extends CreateCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:entity')
            ->setDescription('Create a new doctrine entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'The entity name');
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundleName, $entityName) = $this->getBundleAndEntityName($input, $output);

        if (substr($bundleName, -6) !== 'Bundle') {
            $bundleName .= 'Bundle';
        }

        $kernel = $this->getContainer()->get('kernel');
        $bundle = $kernel->getBundle($bundleName);

        $meta = $this->getClassMetadataInfo($bundle, $entityName);
        $output->writeln([
            sprintf('Going to create <info>%s</info>.', $meta->name),
            '',
        ]);
        $this->addFields($input, $output, $meta);

        $classFile = $bundle->getPath().'/Entity/'.$entityName.'.php';
        $this->createClass($input, $output, $meta, $classFile);

        $mappingFile = $bundle->getPath().'/Resources/config/doctrine/'.$entityName.'.orm.yml';
        $this->createMappingFile($input, $output, $meta, $mappingFile);

        // $this->createRepository();
    }

    protected function getClassMetadataInfo(BundleInterface $bundle, $entityName)
    {
        $class = $bundle->getNamespace().'\\Entity\\'.$entityName;
        $meta = new ClassMetadataInfo($class);
        $meta->mapField([
            'fieldName' => 'id',
            'type' => 'guid',
            'id' => true,
        ]);

        return $meta;
    }

    protected function addFields(InputInterface $input, OutputInterface $output, ClassMetadataInfo $meta)
    {
        $types = array_keys(Type::getTypesMap());
        $output->writeln([
            '<comment>Additional fields</comment>',
            '',
            'You can define fields on your entity now, which will create mappings and class methods automatically.',
            'There is already an <info>id</info> field.',
            '',
            'Available field types: ',
            '',
            sprintf('<info>%s</info>', implode('</info>, <info>', $types)),
            '',
        ]);

        $helper = $this->getHelper('question');

        $q = new Question('Field name (leave empty to finish): ');
        while ($fieldName = $helper->ask($input, $output, $q)) {
            $meta->mapField($this->newField($input, $output, $meta, $fieldName, $types));
        }
    }

    protected function newField(InputInterface $input, OutputInterface $output, ClassMetadataInfo $meta, $name, array $types)
    {
        $helper = $this->getHelper('question');
        $field = ['fieldName' => $name];

        $q = new Question('Type (string): ', 'string');
        $q->setAutocompleterValues($types);
        $q->setValidator(function ($type) use ($types) {
            if (!in_array($type, $types)) {
                throw new \Exception(sprintf('"%s" is not a valid type.', $type));
            }

            return $type;
        });
        $field['type'] = $helper->ask($input, $output, $q);

        $q = new ConfirmationQuestion('Nullable (y/N): ', false);
        if ($helper->ask($input, $output, $q)) {
            $field['nullable'] = true;
        }

        $q = new ConfirmationQuestion('Unique (y/N): ', false);
        if ($helper->ask($input, $output, $q)) {
            $field['nullable'] = true;
        }

        return $field;
    }

    protected function getBundleAndEntityName(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getArgument('entity');
        if (!$entity) {
            $question = new Question('Entity name: (e.g. AppBundle:Item) ');
            $question->setValidator(function ($val) {
                if (count(explode(':', $val)) !== 2) {
                    throw new \RuntimeException(sprintf('The entity must be of the form Bundle:EntityName ("%s" given)', $val));
                }

                return $val;
            });
            $entity = $this->getHelper('question')->ask($input, $output, $question);
        }

        if (count(explode(':', $entity)) !== 2) {
            throw new \RuntimeException(sprintf('The entity must be of the form Bundle:EntityName ("%s" given)', $entity));
        }

        return explode(':', $entity);
    }

    protected function createClass(InputInterface $input, OutputInterface $output, ClassMetadataInfo $meta, $file)
    {
        $gen = new EntityGenerator();
        $gen->setNumSpaces(4);
        $gen->setGenerateStubMethods(true);
        $gen->setGenerateAnnotations(false);
        $gen->setRegenerateEntityIfExists(false);
        $gen->setUpdateEntityIfExists(true);
        $gen->setFieldVisibility(EntityGenerator::FIELD_VISIBLE_PROTECTED);

        $code = trim($gen->generateEntityClass($meta)).PHP_EOL;
        $this->dumpFile($input, $output, $file, $code);
    }

    protected function createMappingFile(InputInterface $input, OutputInterface $output, ClassMetadataInfo $meta, $file)
    {
        $exporter = (new ClassMetadataExporter())->getExporter('yaml');

        $code = $exporter->exportClassMetadata($meta);
        $this->dumpFile($input, $output, $file, $code);
    }
}
