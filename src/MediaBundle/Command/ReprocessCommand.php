<?php

namespace Perform\MediaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\MediaBundle\Importer\FileImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

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
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $media = $this->em->getRepository('PerformMediaBundle:File')->findAll();
        $count = 0;
        foreach ($media as $file) {
            $output->writeln(sprintf('Processing <info>%s</info>', $file->getName()));
            $this->manager->reprocess($file);
            $count++;
        }

        $output->writeln(['', sprintf('Processed <info>%d</info> media %s.', $count, $count === 1 ? 'item' : 'items')]);
    }
}
