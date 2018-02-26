<?php

namespace NotificationBundle\Tests\Publisher;

use Perform\NotificationBundle\Publisher\LocalPublisher;
use Perform\NotificationBundle\Notification;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Perform\NotificationBundle\Renderer\RendererInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalPublisherTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $renderer;
    protected $publisher;

    public function setUp()
    {
        $this->entityManager = $this->getMock(ObjectManager::class);
        $this->renderer = $this->getMock(RendererInterface::class);
        $this->publisher = new LocalPublisher($this->entityManager, $this->renderer);
    }

    public function testSend()
    {
        $recipient1 = $this->getMock(RecipientInterface::class);
        $recipient2 = $this->getMock(RecipientInterface::class);
        $notification = new Notification([$recipient1, $recipient2], 'foo');
        $this->renderer->expects($this->once())
            ->method('getTemplateName')
            ->with('local', $notification)
            ->will($this->returnValue('some_template'));
        $this->renderer->expects($this->exactly(2))
            ->method('renderTemplate')
            ->withConsecutive(
                ['some_template', $notification, $recipient1],
                ['some_template', $notification, $recipient2]
            );
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->publisher->send($notification);
    }
}
