<?php

namespace Perform\NotificationBundle\Notifier;

use Perform\NotificationBundle\Notification;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TraceableNotifier extends Notifier
{
    protected $sent = [];

    /**
     * @return array
     */
    public function getPublisherClasses()
    {
        $classes = [];
        foreach ($this->publishers as $name => $publisher) {
            $classes[$name] = get_class($publisher);
        }

        return $classes;
    }

    public function send(Notification $notification, array $publishers = [])
    {
        parent::send($notification, $publishers);

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
