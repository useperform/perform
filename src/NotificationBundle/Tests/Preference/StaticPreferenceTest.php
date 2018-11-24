<?php

namespace Perform\NotificationBundle\Tests\Preference;

use Perform\NotificationBundle\Preference\StaticPreference;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StaticPreferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testAlwaysTrue()
    {
        $prefs = new StaticPreference(true);
        $recipient = $this->getMock(RecipientInterface::class);
        $notification = new Notification($recipient, 'test');
        $this->assertTrue($prefs->wantsNotification($recipient, $notification));
    }

    public function testAlwaysFalse()
    {
        $prefs = new StaticPreference(false);
        $recipient = $this->getMock(RecipientInterface::class);
        $notification = new Notification($recipient, 'test');
        $this->assertFalse($prefs->wantsNotification($recipient, $notification));
    }
}
