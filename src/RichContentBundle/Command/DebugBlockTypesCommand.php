<?php

namespace Perform\RichContentBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DebugBlockTypesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:debug:block-types')
            ->setDescription('Show available block types for rich content.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $types = $this->getContainer()->get('perform_rich_content.block_type_registry')->all();

        $table = new Table($output);
        $table->setHeaders(['Type', 'Class']);
        foreach ($types as $name => $type) {
            $table->addRow([$name, get_class($type)]);
        }

        $table->render();

        $output->writeln([
            '',
            'To change which block types are enabled, update the <info>perform_rich_content.block_types</info> setting.',
        ]);
    }
}
