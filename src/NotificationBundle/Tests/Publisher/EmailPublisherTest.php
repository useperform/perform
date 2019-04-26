<?php

namespace Perform\NotificationBundle\Tests\Publisher;

use PHPUnit\Framework\TestCase;
use Perform\NotificationBundle\Publisher\EmailPublisher;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Perform\NotificationBundle\Renderer\RendererInterface;
use Perform\NotificationBundle\Preference\PreferenceInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailPublisherTest extends TestCase
{
    private $mailer;
    private $renderer;
    private $publisher;
    private $prefs;

    public function setUp()
    {
        $this->mailer = $this->getMockBuilder(\Swift_Mailer::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->renderer = $this->createMock(RendererInterface::class);
        $this->prefs = $this->createMock(PreferenceInterface::class);
        $this->prefs->expects($this->any())
            ->method('wantsNotification')
            ->will($this->returnValue(true));

        $this->publisher = new EmailPublisher($this->mailer, $this->renderer, $this->prefs, ['app@example.com' => 'Sender']);
    }

    private function mockRecipient($email)
    {
        $recipient = $this->createMock(RecipientInterface::class);
        $recipient->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($email));

        return $recipient;
    }

    public function testSend()
    {
        $recipient1 = $this->mockRecipient('1@example.com');
        $recipient2 = $this->mockRecipient('2@example.com');
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

    public function testNotSentWhenNotWanted()
    {
        $prefs = $this->createMock(PreferenceInterface::class);
        $prefs->expects($this->any())
            ->method('wantsNotification')
            ->will($this->returnValue(false));

        $publisher = new EmailPublisher($this->mailer, $this->renderer, $prefs, ['app@example.com' => 'Sender']);

        $recipient = $this->mockRecipient('user@example.com');
        $notification = new Notification($recipient, 'foo', ['subject' => 'Test subject']);
        $this->mailer->expects($this->never())
            ->method('send');

        $publisher->send($notification);
    }
}
