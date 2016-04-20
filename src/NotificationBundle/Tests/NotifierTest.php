<?php

namespace Admin\NotificationBundle\Tests;

use Admin\NotificationBundle\Notifier;
use Admin\NotificationBundle\Notification;

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
        $this->publisher = $this->getMock('Admin\NotificationBundle\Publisher\PublisherInterface');
        $this->publisher->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testPublisher'));

        $this->notifier->addPublisher($this->publisher);
    }

    protected function newNotification()
    {
        return new Notification($this->getMock('Admin\NotificationBundle\RecipientInterface'), 'test');
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
        $publisher = $this->getMock('Admin\NotificationBundle\Publisher\PublisherInterface');
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
