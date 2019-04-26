<?php

namespace Perform\NotificationBundle\Tests\Notifier;

use PHPUnit\Framework\TestCase;
use Perform\NotificationBundle\Notifier\Notifier;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\NotificationBundle\Notifier\NotifierInterface;
use Perform\NotificationBundle\Event\SendEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NotifierTest extends TestCase
{
    protected $notifier;
    protected $publisher;
    protected $dispatcher;

    public function setUp()
    {
        $this->publisher = $this->createMock(PublisherInterface::class);
        $locator = new LoopableServiceLocator([
            'testPublisher' => function() { return $this->publisher; }
        ]);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->notifier = new Notifier($locator, $this->dispatcher);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(NotifierInterface::class, $this->notifier);
    }

    protected function newNotification($recipientCount = 1)
    {
        $recipients = [];
        while ($recipientCount !== 0) {
            $recipients[] = $this->createMock(RecipientInterface::class);
            $recipientCount--;
        }

        return new Notification($recipients, 'test');
    }

    public function testSend()
    {
        $n = $this->newNotification();
        $this->publisher->expects($this->once())
            ->method('send')
            ->with($n);
        $callback = function ($e) use ($n) {
            return $e instanceof SendEvent
                && $e->getNotification() === $n
                && $e->getPublishers() === ['testPublisher'];
        };
        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [SendEvent::PRE_SEND, $this->callback($callback)],
                [SendEvent::POST_SEND, $this->callback($callback)]
            );

        $this->notifier->send($n, ['testPublisher']);
    }
}
