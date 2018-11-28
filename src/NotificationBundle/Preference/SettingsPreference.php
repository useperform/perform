<?php

namespace Perform\NotificationBundle\Preference;

use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsPreference implements PreferenceInterface
{
    private $settings;
    private $prefix;
    private $default;

    public function __construct(SettingsManagerInterface $settings, $prefix, $default)
    {
        $this->settings = $settings;
        $this->prefix = $prefix;
        $this->default = (bool) $default;
    }

    public function wantsNotification(RecipientInterface $recipient, Notification $notification)
    {
        if (!$recipient instanceof UserInterface) {
            return false;
        }

        return (bool) $this->settings->getUserValue($recipient, $this->prefix.$notification->getType(), $this->default);
    }
}
