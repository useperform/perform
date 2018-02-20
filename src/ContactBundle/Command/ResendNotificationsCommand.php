<?php

namespace Perform\ContactBundle\Command;

use Perform\BaseBundle\Doctrine\RepositoryResolver;
use Perform\ContactBundle\Form\Handler\ContactFormHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ResendNotificationsCommand extends Command
{
    protected $repoResolver;
    protected $handler;

    public function __construct(RepositoryResolver $repoResolver, ContactFormHandler $handler)
    {
        $this->repoResolver = $repoResolver;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform:contact:resend-notifications')
            ->setDescription('Resend the notifications for a contact form message')
            ->addArgument('message_id', InputArgument::REQUIRED)
            ->addOption('force', '', InputOption::VALUE_NONE, 'Send the notifications even if the message is marked as spam.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->repoResolver->getRepository('PerformContactBundle:Message');
        $id = $input->getArgument('message_id');
        $message = $repo->find($id);
        if (!$message) {
            throw new \Exception(sprintf('Contact message with id "%s" was not found.', $id));
        }
        if ($message->isSpam() && !$input->getOption('force')) {
            $output->writeln(sprintf('Not sending notifications for this message from <info>%s</info>, it has been marked as spam. Pass <info>--force</info> to override.', $message->getEmail()));

            return 1;
        }

        $this->handler->sendNotifications($message);

        $output->writeln(sprintf('Resent notifications for message <info>%s</info> from <info>%s</info>.', $id, $message->getEmail()));
    }
}
