<?php

namespace Perform\MediaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\MediaBundle\Importer\FileImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class ReprocessCommand extends Command
{
    protected $manager;
    protected $em;

    public function __construct(FileImporter $manager, EntityManagerInterface $em)
    {
        $this->manager = $manager;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform:media:reprocess')
            ->setDescription('Fetch media and process it again')
            ->addArgument('file_id', InputArgument::OPTIONAL)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->em->getRepository('PerformMediaBundle:File');
        $fileId = $input->getArgument('file_id');
        if ($fileId) {
            $file = $repo->find($fileId);
            if (!$file) {
                throw new \Exception(sprintf('File with id "%s" was not found.', $fileId));
            }

            $media = [$file];
        } else {
            $media = $repo->findAll();
        }

        $count = 0;
        foreach ($media as $file) {
            $output->writeln(sprintf('Processing <info>%s</info>', $file->getName()));
            $this->manager->reprocess($file);
            $count++;
        }

        $output->writeln(['', sprintf('Processed <info>%d</info> media %s.', $count, $count === 1 ? 'item' : 'items')]);
    }
}
