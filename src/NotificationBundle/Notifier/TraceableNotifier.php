<?php

namespace Perform\NotificationBundle\Notifier;

use Perform\NotificationBundle\Notification;

/**
 * TraceableNotifier.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TraceableNotifier extends Notifier
{
    protected $sent = [];

    /**
     * @return PublisherInterface[]
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    public function send(Notification $notification, array $publishers = [])
    {
        parent::send($notification, $publishers);

        if (empty($publishers)) {
            $publishers = $this->defaultPublishers;
        }

        $this->sent[] = [$notification, $publishers];
    }

    /**
     * @return array
     */
    public function getSent()
    {
        return $this->sent;
    }
}
