<?php

namespace Perform\NotificationBundle\Event;

use Perform\NotificationBundle\Notification;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SendEvent extends Event
{
    const PRE_SEND = 'perform_notification.pre_send';
    const POST_SEND = 'perform_notification.post_send';

    protected $notification;
    protected $publishers;

    public function __construct(Notification $notification, array $publishers)
    {
        $this->notification = $notification;
        $this->publishers = $publishers;
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function getPublishers()
    {
        return $this->publishers;
    }
}
