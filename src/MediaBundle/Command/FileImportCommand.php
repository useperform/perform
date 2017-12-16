<?php

namespace Perform\MediaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputOption;

/**
 * FileImportCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class FileImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName( 'perform:media:import')
            ->setDescription('Add files to the media library.')
            ->addArgument(
                'paths',
                InputArgument::IS_ARRAY,
                'The paths to files or directories'
            )
            ->addOption(
                'extension',
                '',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Only the given extensions'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importer = $this->getContainer()->get('perform_media.importer.file');
        $extensions = $input->getOption('extension');
        foreach ($input->getArgument('paths') as $path) {
            if (is_dir($path)) {
                $importer->importDirectory($path, $extensions);
            }
            if (is_file($path)) {
                $importer->importFile($path);
            }

            $output->writeln(sprintf('Imported <info>%s</info>.', $path));
        }
    }
}
