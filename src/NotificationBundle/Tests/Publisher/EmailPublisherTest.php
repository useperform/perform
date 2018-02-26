<?php

namespace Perform\NotificationBundle\Tests\Publisher;

use Perform\NotificationBundle\Publisher\EmailPublisher;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Perform\BaseBundle\Email\Mailer;
use Perform\NotificationBundle\Renderer\RendererInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailPublisherTest extends \PHPUnit_Framework_TestCase
{
    protected $mailer;
    protected $renderer;
    protected $publisher;

    public function setUp()
    {
        $this->mailer = $this->getMockBuilder(Mailer::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->renderer = $this->getMock(RendererInterface::class);
        $this->publisher = new EmailPublisher($this->mailer, $this->renderer);
    }

    public function testSend()
    {
        $recipient1 = $this->getMock(RecipientInterface::class);
        $recipient1->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('1@example.com'));
        $recipient2 = $this->getMock(RecipientInterface::class);
        $recipient2->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('2@example.com'));
        $notification = new Notification([$recipient1, $recipient2], 'foo', ['subject' => 'Test subject']);
        $this->renderer->expects($this->once())
            ->method('getTemplateName')
            ->with('email', $notification)
            ->will($this->returnValue('some_template'));
        $message = new \Swift_Message();
        $this->mailer->expects($this->exactly(2))
            ->method('createMessage')
            ->withConsecutive(
                ['1@example.com', 'Test subject', 'some_template'],
                ['2@example.com', 'Test subject', 'some_template']
            )
            ->will($this->returnValue($message));

        $this->mailer->expects($this->exactly(2))
            ->method('sendMessage')
            ->with($message);

        $this->publisher->send($notification);
    }
}
