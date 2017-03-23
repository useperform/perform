<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;

/**
 * ActionListCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:action:list')
            ->setDescription('List available actions')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actions = $this->getContainer()->get('perform_base.action_registry')->getAll();

        $table = new Table($output);
        $table->setHeaders(['Action name', 'Class']);
        foreach ($actions as $name => $action) {
            $table->addRow([$name, get_class($action)]);
        }

        $table->render();
    }
}
