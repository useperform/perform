<?php

namespace Perform\NotificationBundle\Preference;

use Perform\NotificationBundle\Recipient\RecipientInterface;
use Perform\NotificationBundle\Notification;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface PreferenceInterface
{
    /**
     * Check if a recipient wants to be sent a particular notification.
     *
     * This decision may be made by looking at user settings, the current time, an environment variable, etc.
     *
     * @return bool
     */
    public function wantsNotification(RecipientInterface $recipient, Notification $notification);
}
