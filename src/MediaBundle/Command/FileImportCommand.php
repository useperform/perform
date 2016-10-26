<?php

namespace Perform\MediaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Finder\Finder;

/**
 * FileImportCommand.
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
                 'paths',
                 InputArgument::IS_ARRAY,
                 'The paths to files or directories'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importer = $this->getContainer()->get('perform_media.importer.file');
        foreach ($this->getFiles($input->getArgument('paths')) as $path) {
            $importer->import($path);
            $output->writeln(sprintf('Imported <info>%s</info>', $path));
        }
    }

    protected function getFiles(array $paths)
    {
        $files = [];
        $finder = new Finder();
        $found = [];
        $dirs = 0;

        foreach ($paths as $path) {
            if (is_file($path)) {
                $files[] = $path;
                continue;
            }

            $finder->in($path);
            $dirs++;
        }

        //finder will error out if in() hasn't been called
        if ($dirs > 0) {
            $found = array_map(function ($file) {
                return $file->getRealPath();
            }, array_values(iterator_to_array($finder->files())));
        }

        return array_unique(array_merge($files, $found));
    }
}
