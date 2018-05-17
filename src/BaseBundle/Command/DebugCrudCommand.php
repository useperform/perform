<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DebugCrudCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:debug:crud')
            ->setDescription('Show available crud services')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $registry = $this->getContainer()->get('perform_base.crud.registry');
        $cruds = $registry->all();

        $table = new Table($output);
        $table->setHeaders(['Entity', 'Crud service', 'Crud class']);
        foreach ($cruds as $entity => $service) {
            $crud = $registry->get($entity);
            $table->addRow([$entity, $service, get_class($crud)]);
        }

        $table->render();
    }
}
