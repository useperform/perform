<?php

namespace Perform\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PurgeResetTokensCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:purge-reset-tokens')
            ->setDescription('Remove expired password reset tokens from the database.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->getContainer()->get('perform_user.reset_token_manager')
                  ->removeStaleTokens(new \DateTime());

        $output->writeln(sprintf('Removed <info>%s</info> expired %s from the database.', $count, $count === 1 ? 'token' : 'tokens'));
    }
}
