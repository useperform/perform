<?php

namespace Perform\NotificationBundle\Tests\Publisher;

use Perform\NotificationBundle\Publisher\EmailPublisher;
use Perform\NotificationBundle\Notification;

/**
 * EmailPublisherTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailPublisherTest extends \PHPUnit_Framework_TestCase
{
    protected $mailer;
    protected $templating;
    protected $publisher;

    public function setUp()
    {
        $this->mailer = $this->getMockBuilder('Perform\BaseBundle\Email\Mailer')
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->templating = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $this->publisher = new EmailPublisher($this->mailer, $this->templating);
    }

    protected function newNotification($type)
    {
        $recipient = $this->getMock('Perform\NotificationBundle\RecipientInterface');
        $recipient->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('test@example.com'));

        return new Notification(
            $recipient,
            $type,
            ['subject' => 'Test subject']);
    }

    public function testSend()
    {
        $message = new \Swift_Message();
        $this->mailer->expects($this->once())
            ->method('createMessage')
            ->with('test@example.com', 'Test subject', 'PerformNotificationBundle:test:email.html.twig')
            ->will($this->returnValue($message));
        $this->mailer->expects($this->once())
            ->method('sendMessage')
            ->with($message);

        $this->publisher->send($this->newNotification('test'));
    }

    public function testSendWithNamespacing()
    {
        $message = new \Swift_Message();
        $this->mailer->expects($this->once())
            ->method('createMessage')
            ->with('test@example.com', 'Test subject', 'PerformBaseBundle:notifications:crud_update/email.html.twig')
            ->will($this->returnValue($message));
        $this->mailer->expects($this->once())
            ->method('sendMessage')
            ->with($message);

        $this->publisher->send($this->newNotification('PerformBaseBundle:crud_update'));
    }
}
