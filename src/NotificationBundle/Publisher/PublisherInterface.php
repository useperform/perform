<?php

namespace Admin\NotificationBundle\Publisher;

use Admin\NotificationBundle\Notification;

interface PublisherInterface
{
    function send(Notification $notification);

    function getName();
}
