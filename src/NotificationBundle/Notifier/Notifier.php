<?php

namespace Perform\NotificationBundle\Notifier;

use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Notification;

class Notifier
{
    protected $publishers = [];
    protected $defaultPublishers = [];

    public function addPublisher(PublisherInterface $publisher)
    {
        $this->publishers[$publisher->getName()] = $publisher;
    }

    /**
     * Set the publishers to use when a notification is sent without
     * any.
     *
     * @param array $publishers The names of the publishers to use.
     */
    public function setDefaultPublishers(array $publishers)
    {
        $this->defaultPublishers = $publishers;
    }

    /**
     * Send a notification.
     *
     * @param Notification $notification
     * @param array $publishers The publishers to use (leave blank to use default publishers)
     */
    public function send(Notification $notification, array $publishers = [])
    {
        if (empty($publishers)) {
            $publishers = $this->defaultPublishers;
        }

        foreach ($publishers as $name) {
            if (!isset($this->publishers[$name])) {
                throw new \Exception(sprintf('Unknown notification publisher "%s"', $name));
            }

            $this->publishers[$name]->send($notification);
        }
    }
}
