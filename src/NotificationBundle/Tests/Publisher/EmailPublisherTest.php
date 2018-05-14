<?php

namespace Perform\NotificationBundle\Tests\Publisher;

use Perform\NotificationBundle\Publisher\EmailPublisher;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
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
        $this->mailer = $this->getMockBuilder(\Swift_Mailer::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->renderer = $this->getMock(RendererInterface::class);
        $this->publisher = new EmailPublisher($this->mailer, $this->renderer, ['app@example.com' => 'Sender']);
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
        $this->renderer->expects($this->exactly(2))
            ->method('renderTemplate')
            ->withConsecutive(
                ['some_template', $notification, $recipient1],
                ['some_template', $notification, $recipient2]
            )
            ->will($this->returnValue('Rendered email'));
        $sent = [];
        $this->mailer->expects($this->exactly(2))
            ->method('send')
            ->with($this->callback(function ($message) use (&$sent) {
                $sent[] = $message;

                return true;
            }));

        $this->publisher->send($notification);

        $this->assertInstanceOf(\Swift_Message::class, $sent[0]);
        $this->assertSame(['1@example.com'], array_keys($sent[0]->getTo()));
        $this->assertSame(['app@example.com' => 'Sender'], $sent[0]->getFrom());
        $this->assertSame('Test subject', $sent[0]->getSubject());
        $this->assertSame('Rendered email', $sent[0]->getBody());

        $this->assertInstanceOf(\Swift_Message::class, $sent[1]);
        $this->assertSame(['2@example.com'], array_keys($sent[1]->getTo()));
        $this->assertSame(['app@example.com' => 'Sender'], $sent[1]->getFrom());
        $this->assertSame('Test subject', $sent[1]->getSubject());
        $this->assertSame('Rendered email', $sent[1]->getBody());
    }
}
