<?php

namespace Perform\NotificationBundle\Preference;

use Perform\NotificationBundle\Recipient\RecipientInterface;
use Perform\NotificationBundle\Notification;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StaticPreference implements PreferenceInterface
{
    private $preference;

    public function __construct($preference)
    {
        $this->preference = (bool) $preference;
    }

    public function wantsNotification(RecipientInterface $recipient, Notification $notification)
    {
        return $this->preference;
    }
}
