<?php

namespace Perform\NotificationBundle\Tests\Entity;

use Perform\NotificationBundle\Entity\NotificationLog;

/**
 * NotificationLogTest
 **/
class NotificationLogTest extends \PHPUnit_Framework_TestCase
{
    public function testSetRecipient()
    {
        $recipient = $this->getMock('Perform\NotificationBundle\RecipientInterface');
        $recipient->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('some_guid'));

        $log = new NotificationLog();
        $this->assertSame($log, $log->setRecipient($recipient));
        $this->assertSame('some_guid', $log->getRecipientId());
    }
}
