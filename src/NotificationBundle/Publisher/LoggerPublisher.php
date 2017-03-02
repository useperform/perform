<?php

namespace Perform\NotificationBundle\Publisher;

use Psr\Log\LoggerInterface;
use Perform\NotificationBundle\Notification;

class LoggerPublisher implements PublisherInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function send(Notification $notification)
    {
        $this->logger->info(sprintf('New notification of type "%s"', $notification->getType()), [
            'recipients' => $notification->getRecipients()
        ]);
    }

    public function getName()
    {
        return 'logger';
    }
}
