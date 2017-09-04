<?php

namespace Perform\NotificationBundle\Notifier;

use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Notification;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

class Notifier
{
    protected $publishers = [];
    protected $logger;
    protected $logLevel = LogLevel::INFO;

    public function addPublisher(PublisherInterface $publisher)
    {
        $this->publishers[$publisher->getName()] = $publisher;
    }

    /**
     * @param LoggerInterface|null $logger
     * @param string $logLevel
     */
    public function setLogger(LoggerInterface $logger = null, $logLevel = LogLevel::INFO)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    /**
     * Send a notification.
     *
     * @param Notification $notification
     * @param array $publishers The publishers to use (leave blank to use default publishers)
     */
    public function send(Notification $notification, array $publishers = [])
    {
        foreach ($publishers as $name) {
            if (!isset($this->publishers[$name])) {
                throw new \Exception(sprintf('Unknown notification publisher "%s". Available publishers are "%s". You may need to set configuration to enable additional publishers.', $name, implode('", "', array_keys($this->publishers))));
            }

            $this->publishers[$name]->send($notification);
        }

        if ($this->logger) {
            $this->logger->log($this->logLevel, sprintf('Sent notification of type "%s".', $notification->getType()), [
                'recipients' => $notification->getRecipients(),
                'publishers' => $publishers,
            ]);
        }
    }
}
