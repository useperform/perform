<?php

namespace Perform\NotificationBundle\Tests\Preference;

use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Preference\SettingsPreference;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsPreferenceTest extends \PHPUnit_Framework_TestCase
{
    private $settings;

    public function setUp()
    {
        $this->settings = $this->getMock(SettingsManagerInterface::class);
    }

    public function testUsesManager()
    {
        $prefs = new SettingsPreference($this->settings, 'notify.', false);
        $recipient = $this->getMock([RecipientInterface::class, UserInterface::class]);

        $this->settings->expects($this->exactly(2))
            ->method('getUserValue')
            ->withConsecutive(
                [$recipient, $this->equalTo('notify.weekly_update'), false],
                [$recipient, $this->equalTo('notify.daily_update'), false]
            )
            ->willReturnOnConsecutiveCalls(true, false);

        $this->assertTrue($prefs->wantsNotification($recipient, new Notification($recipient, 'weekly_update')));
        $this->assertFalse($prefs->wantsNotification($recipient, new Notification($recipient, 'daily_update')));
    }
}
