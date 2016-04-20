<?php

namespace Admin\NotificationBundle\Tests\Entity;

use Admin\NotificationBundle\Entity\NotificationLog;

/**
 * NotificationLogTest
 **/
class NotificationLogTest extends \PHPUnit_Framework_TestCase
{
    public function testSetRecipient()
    {
        $recipient = $this->getMock('Admin\NotificationBundle\RecipientInterface');
        $recipient->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('some_guid'));

        $log = new NotificationLog();
        $this->assertSame($log, $log->setRecipient($recipient));
        $this->assertSame('some_guid', $log->getRecipientId());
    }
}
