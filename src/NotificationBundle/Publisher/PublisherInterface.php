<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\NotificationBundle\Notification;

interface PublisherInterface
{
    function send(Notification $notification);

    function getName();
}
