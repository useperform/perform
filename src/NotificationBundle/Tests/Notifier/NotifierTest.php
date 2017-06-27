<?php

namespace Perform\NotificationBundle\Tests\Notifier;

use Perform\NotificationBundle\Notifier\Notifier;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

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

    public function testSendWithLogging()
    {
        $n = $this->newNotification();
        $logger = $this->getMock(LoggerInterface::class);
        $this->notifier->setLogger($logger);
        $logger->expects($this->once())
            ->method('log')
            ->with('info', 'Sent notification of type "test".', [
                'recipients' => $n->getRecipients(),
                'publishers' => ['testPublisher'],
            ]);

        $this->notifier->send($n, ['testPublisher']);
    }

    public function testSendWithLogLevel()
    {
        $n = $this->newNotification();
        $logger = $this->getMock(LoggerInterface::class);
        $this->notifier->setLogger($logger, LogLevel::DEBUG);
        $logger->expects($this->once())
            ->method('log')
            ->with('debug', 'Sent notification of type "test".', [
                'recipients' => $n->getRecipients(),
                'publishers' => ['testPublisher'],
            ]);

        $this->notifier->send($n, ['testPublisher']);
    }
}
