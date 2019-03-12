<?php

namespace Perform\NotificationBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Perform\NotificationBundle\EventListener\LogListener;
use Perform\NotificationBundle\Event\SendEvent;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LogListenerTest extends TestCase
{
    protected $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    protected function newNotification($recipientCount = 1)
    {
        $recipients = [];
        while ($recipientCount !== 0) {
            $recipients[] = $this->createMock(RecipientInterface::class);
            --$recipientCount;
        }

        return new Notification($recipients, 'test');
    }

    public function testPostSend()
    {
        $n = $this->newNotification();
        $listener = new LogListener($this->logger);
        $this->logger->expects($this->once())
            ->method('log')
            ->with('info', 'Sent notification of type "test" to 1 recipient.', [
                'type' => 'test',
                'recipient_count' => 1,
                'publishers' => ['testPublisher'],
            ]);

        $event = new SendEvent($n, ['testPublisher']);
        $listener->postSend($event);
    }

    public function testPostSendWithLogLevel()
    {
        $n = $this->newNotification(2);
        $listener = new LogListener($this->logger, LogLevel::DEBUG);
        $this->logger->expects($this->once())
            ->method('log')
            ->with('debug', 'Sent notification of type "test" to 2 recipients.', [
                'type' => 'test',
                'recipient_count' => 2,
                'publishers' => ['testPublisher'],
            ]);

        $event = new SendEvent($n, ['testPublisher']);
        $listener->postSend($event);
    }
}
