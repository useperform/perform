<?php

namespace Perform\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\UserBundle\Security\ResetTokenManager;
use Symfony\Component\Console\Command\Command;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PurgeResetTokensCommand extends Command
{
    protected $tokenManager;

    public function __construct(ResetTokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform:user:purge-reset-tokens')
            ->setDescription('Remove expired password reset tokens from the database.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->tokenManager->removeStaleTokens(new \DateTime());

        $output->writeln(sprintf('Removed <info>%s</info> expired %s from the database.', $count, $count === 1 ? 'token' : 'tokens'));
    }
}
