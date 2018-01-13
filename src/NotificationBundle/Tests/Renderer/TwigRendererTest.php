<?php

namespace Perform\NotificationBundle\Tests\Renderer;

use Twig\Environment;
use Perform\NotificationBundle\Renderer\TwigRenderer;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TwigRendererTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->twig = $this->getMockBuilder(Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->renderer = new TwigRenderer($this->twig);
    }

    public function testGetTemplateName()
    {
        $notification = new Notification($this->getMock(RecipientInterface::class), 'user_welcome');
        $this->assertSame('notifications/user_welcome/email.html.twig', $this->renderer->getTemplateName('email', $notification));
    }

    public function testGetNamespacedTemplateName()
    {
        $notification = new Notification($this->getMock(RecipientInterface::class), 'App:user_welcome');
        $this->assertSame('@App/notifications/user_welcome/email.html.twig', $this->renderer->getTemplateName('email', $notification));

        $notification = new Notification($this->getMock(RecipientInterface::class), 'OtherBundle:user_welcome');
        $this->assertSame('@Other/notifications/user_welcome/email.html.twig', $this->renderer->getTemplateName('email', $notification));

    }
}
