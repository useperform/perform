<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;

/**
 * DebugAdminsCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DebugAdminsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:debug:admins')
            ->setDescription('Show available content admins.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $registry = $this->getContainer()->get('perform_base.admin.registry');
        $admins = $registry->getAdmins();

        $table = new Table($output);
        $table->setHeaders(['Entity', 'Admin service', 'Admin class']);
        foreach ($admins as $entity => $service) {
            $admin = $registry->getAdmin($entity);
            $table->addRow([$entity, $service, get_class($admin)]);
        }

        $table->render();
    }
}
