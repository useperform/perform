<?php

namespace Perform\NotificationBundle\Notifier;

use Perform\NotificationBundle\Notification;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

class Notifier implements NotifierInterface
{
    protected $publishers = [];
    protected $logger;
    protected $logLevel = LogLevel::INFO;

    public function __construct(LoopableServiceLocator $publishers)
    {
        $this->publishers = $publishers;
    }

    /**
     * @param LoggerInterface|null $logger
     * @param string               $logLevel
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
     * @param array        $publishers   The publishers to use
     */
    public function send(Notification $notification, array $publishers)
    {
        foreach ($publishers as $name) {
            if (!$this->publishers->has($name)) {
                throw new \Exception(sprintf('Unknown notification publisher "%s". Available publishers are "%s". You may need to set configuration to enable additional publishers.', $name, implode('", "', $this->publishers->getNames())));
            }

            $this->publishers->get($name)->send($notification);
        }

        if ($this->logger) {
            // don't log the recipients or context, as both may
            // contain personally identifiable information.
            // if you want to log this information, consider
            // implementing NotifierInterface yourself.
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
