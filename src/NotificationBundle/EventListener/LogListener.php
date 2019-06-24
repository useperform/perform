<?php

namespace Perform\NotificationBundle\EventListener;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Perform\NotificationBundle\Event\SendEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LogListener
{
    protected $logger;
    protected $logLevel = LogLevel::INFO;

    public function __construct(LoggerInterface $logger, $logLevel = LogLevel::INFO)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    public function postSend(SendEvent $event)
    {
        $notification = $event->getNotification();
        $publishers = $event->getPublishers();
        if ($this->logger) {
            // don't log the recipients or context, as both may
            // contain personally identifiable information.
            // if you want to log this information, create another
            // listener for the SendEvent::POST_SEND event.
            $type = $notification->getType();
            $recipientCount = count($notification->getRecipients());
            $this->logger->log($this->logLevel, sprintf('Sent notification of type "%s" to %s %s.', $type, $recipientCount, $recipientCount === 1 ? 'recipient' : 'recipients'), [
                'type' => $type,
                'recipient_count' => $recipientCount,
                'publishers' => $publishers,
            ]);
        }
    }
}
