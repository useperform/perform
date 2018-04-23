<?php

namespace Perform\NotificationBundle\Tests\Entity;

use Perform\NotificationBundle\Entity\NotificationLog;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NotificationLogTest extends \PHPUnit_Framework_TestCase
{
    public function testSetRecipient()
    {
        $recipient = $this->getMock(UserInterface::class);
        $log = new NotificationLog();
        $this->assertSame($log, $log->setRecipient($recipient));
        $this->assertSame($recipient, $log->getRecipient());
    }
}
