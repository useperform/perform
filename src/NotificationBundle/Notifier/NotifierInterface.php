<?php

namespace Perform\NotificationBundle\Notifier;

use Perform\NotificationBundle\Notification;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface NotifierInterface
{
    /**
     * Send a notification.
     *
     * @param Notification $notification
     * @param array        $publishers   The publishers to use
     */
    public function send(Notification $notification, array $publishers);
}
