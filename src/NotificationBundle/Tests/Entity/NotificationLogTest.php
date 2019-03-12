<?php

namespace Perform\NotificationBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Perform\NotificationBundle\Entity\NotificationLog;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NotificationLogTest extends TestCase
{
    public function testSetRecipient()
    {
        $recipient = $this->createMock(UserInterface::class);
        $log = new NotificationLog();
        $this->assertSame($log, $log->setRecipient($recipient));
        $this->assertSame($recipient, $log->getRecipient());
    }
}
