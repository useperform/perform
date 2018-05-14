<?php

namespace Perform\NotificationBundle\Tests\Notifier;

use Perform\NotificationBundle\Notifier\Notifier;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\NotificationBundle\Notifier\NotifierInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NotifierTest extends \PHPUnit_Framework_TestCase
{
    protected $notifier;
    protected $publisher;

    public function setUp()
    {
        $this->publisher = $this->getMock(PublisherInterface::class);
        $locator = new LoopableServiceLocator([
            'testPublisher' => function() { return $this->publisher; }
        ]);

        $this->notifier = new Notifier($locator);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(NotifierInterface::class, $this->notifier);
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
