<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;

/**
 * DebugFrontendsCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DebugFrontendsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:debug:frontends')
            ->setDescription('Show available scaffolding frontends.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $frontends = $this->getContainer()->get('perform_dev.frontend_registry')->all();

        $table = new Table($output);
        $table->setHeaders(['Frontend', 'Class']);
        foreach ($frontends as $name => $frontend) {
            $table->addRow([$name, get_class($frontend)]);
        }

        $table->render();
    }
}
