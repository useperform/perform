<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;

/**
 * DebugTypesCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DebugTypesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:debug:types')
            ->setDescription('Show available content types.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actions = $this->getContainer()->get('perform_base.type_registry')->getAll();

        $table = new Table($output);
        $table->setHeaders(['Type', 'Class']);
        foreach ($actions as $name => $action) {
            $table->addRow([$name, get_class($action)]);
        }

        $table->render();
    }
}
