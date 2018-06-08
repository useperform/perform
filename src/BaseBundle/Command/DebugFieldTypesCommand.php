<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DebugFieldTypesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:debug:field-types')
            ->setDescription('Show available field types.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $types = $this->getContainer()->get('perform_base.field_type_registry')->getAll();

        $table = new Table($output);
        $table->setHeaders(['Name', 'Class']);
        foreach ($types as $name => $type) {
            $table->addRow([$name, get_class($type)]);
        }

        $table->render();
    }
}
