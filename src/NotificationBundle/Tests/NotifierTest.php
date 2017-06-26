<?php

namespace Perform\NotificationBundle\Tests;

use Perform\NotificationBundle\Notifier;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Recipient\RecipientInterface;

/**
 * NotifierTest
 **/
class NotifierTest extends \PHPUnit_Framework_TestCase
{
    protected $notifier;
    protected $publisher;

    public function setUp()
    {
        $this->notifier = new Notifier();
        $this->publisher = $this->getMock(PublisherInterface::class);
        $this->publisher->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testPublisher'));

        $this->notifier->addPublisher($this->publisher);
    }

    protected function newNotification()
    {
        return new Notification($this->getMock(RecipientInterface::class), 'test');
    }

    public function testSend()
    {
        $n = $this->newNotification();
        $this->publisher->expects($this->once())
            ->method('send')
            ->with($n);
        $this->notifier->send($n, ['testPublisher']);
    }

    public function testSendWithDefaultPublishers()
    {
        $n = $this->newNotification();
        $this->publisher->expects($this->once())
            ->method('send')
            ->with($n);
        $this->notifier->setDefaultPublishers(['testPublisher']);
        $this->notifier->send($n);
    }

    public function testDefaultPublishersCanBeOveridden()
    {
        $n = $this->newNotification();
        $publisher = $this->getMock('Perform\NotificationBundle\Publisher\PublisherInterface');
        $publisher->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mySuperPublisher'));
        $this->notifier->addPublisher($publisher);
        $this->publisher->expects($this->never())
            ->method('send');

        $this->notifier->setDefaultPublishers(['testPublisher', 'someOtherPublisher']);
        $this->notifier->send($n, ['mySuperPublisher']);
    }
}
