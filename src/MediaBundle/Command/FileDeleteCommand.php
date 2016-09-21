<?php

namespace Perform\MediaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * FileDeleteCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class FileDeleteCommand extends ContainerAwareCommand
{
    protected $name = 'admin:media:delete';
    protected $description = 'Delete a file from the media library.';

    protected function configure()
    {
        $this->setName($this->name)
             ->setDescription($this->description)
             ->addArgument(
                 'id',
                 InputArgument::REQUIRED,
                 'The id of the file in the database'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $importer = $container->get('admin_media.importer.file');
        $file = $container->get('doctrine.orm.entity_manager')->getRepository('PerformMediaBundle:File')->find($input->getArgument('id'));

        if (!$file) {
            throw new \Exception('File not found.');
        }

        $importer->delete($file);
        $output->writeln(sprintf('Deleted <info>%s</info> from the media library.', $file->getName()));
    }
}
