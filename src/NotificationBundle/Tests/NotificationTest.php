<?php

namespace Admin\NotificationBundle\Tests;

use Admin\NotificationBundle\Notification;

/**
 * NotificationTest
 **/
class NotificationTest extends \PHPUnit_Framework_TestCase
{
    protected function mockRecipient()
    {
        return $this->getMock('Admin\NotificationBundle\RecipientInterface');
    }

    public function testGetters()
    {
        $n = new Notification($this->mockRecipient(), 'test', ['foo' => 'bar']);
        $this->assertSame('test', $n->getType());
        $this->assertSame(['foo' => 'bar'], $n->getContext());
    }

    public function testSingleRecipient()
    {
        $recipient = $this->mockRecipient();
        $n = new Notification($recipient, 'test', ['foo' => 'bar']);
        $this->assertSame([$recipient], $n->getRecipients());
    }

    public function testManyRecipients()
    {
        $user = $this->mockRecipient();
        $user2 = $this->mockRecipient();
        $user3 = $this->mockRecipient();
        $n = new Notification([$user, $user2, $user3], 'test', ['foo' => 'bar']);
        $this->assertSame([$user, $user2, $user3], $n->getRecipients());
    }

    public function testInvalidRecipientThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $n = new Notification(new \DateTime(), 'test');
    }
}
