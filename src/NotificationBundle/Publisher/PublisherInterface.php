<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\NotificationBundle\Notification;

interface PublisherInterface
{
    /**
     * Send a notification.
     *
     * @param Notification $notification
     */
    public function send(Notification $notification);
}
