<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:action:run')
            ->setDescription('Run an action on an entity or list of entities')
            ->addArgument('crudName', InputArgument::REQUIRED, 'The crud name')
            ->addArgument('action', InputArgument::REQUIRED)
            ->addArgument('entity', InputArgument::IS_ARRAY, 'An entity id or list of entity ids')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // authenticate as a basic user so the action runner will grant permission to run actions
        $this->getContainer()->get('security.token_storage')
            ->setToken(new PreAuthenticatedToken('console_user', null, 'console', ['ROLE_USER']));

        $response = $this->getContainer()->get('perform_base.action_runner')
                  ->run($input->getArgument('crudName'), $input->getArgument('action'), $input->getArgument('entity'));

        $output->writeln($response->getMessage());
    }
}
