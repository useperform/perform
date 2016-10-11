<?php

namespace Perform\MediaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * FileImportCommand
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class FileImportCommand extends ContainerAwareCommand
{
    protected $name = 'perform:media:import';
    protected $description = 'Add files to the media library.';

    protected function configure()
    {
        $this->setName($this->name)
             ->setDescription($this->description)
             ->addArgument(
                 'path',
                 InputArgument::REQUIRED,
                 'The path to the file or directory'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importer = $this->getContainer()->get('perform_media.importer.file');
        foreach ($this->getFiles($input->getArgument('path')) as $file) {
            $importer->import($file->getPathname());
            $output->writeln(sprintf('Imported <info>%s</info>', $file->getPathname()));
        }
    }

    protected function getFiles($path)
    {
        if (!is_dir($path)) {
            return [new \SplFileObject($path)];
        }

        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $path, RecursiveDirectoryIterator::SKIP_DOTS
            )
        );
    }
}
