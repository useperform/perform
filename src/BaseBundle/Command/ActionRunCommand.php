<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * ActionRunCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:action:run')
            ->setDescription('Run an action on an entity or list of entities')
            ->addArgument('action', InputArgument::REQUIRED)
            ->addArgument('entityClass', InputArgument::REQUIRED, 'The entity name or class, e.g. PerformUserBundle:User')
            ->addArgument('entity', InputArgument::IS_ARRAY, 'An entity id or list of entity ids')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->getContainer()->get('perform_base.action_runner')
                  ->run($input->getArgument('action'), $input->getArgument('entityClass'), $input->getArgument('entity'));

        $output->writeln($response->getMessage());
    }
}
