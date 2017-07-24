<?php

namespace Perform\MailingListBundle\EventListener;

use Perform\MailingListBundle\SubscriberManager;
use Psr\Log\LoggerInterface;

/**
 * Flush new mailing list subscribers on kernel.terminate to avoid
 * holding up the response.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FlushSubscribersListener
{
    protected $manager;

    public function __construct(SubscriberManager $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    public function onKernelTerminate($event)
    {
        try {
            $this->manager->flush();
        } catch (\Exception $e) {
            $this->logger->critical('An error occurred saving new mailing list subscribers.', [
                'error' => $e,
            ]);
        }
    }
}
