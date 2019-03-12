<?php

namespace Perform\NotificationBundle\Tests\Renderer;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Perform\NotificationBundle\Renderer\TwigRenderer;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TwigRendererTest extends TestCase
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
        $notification = new Notification($this->createMock(RecipientInterface::class), 'user_welcome');
        $this->assertSame('notification/user_welcome/email.html.twig', $this->renderer->getTemplateName('email', $notification));
    }

    public function testGetNamespacedTemplateName()
    {
        $notification = new Notification($this->createMock(RecipientInterface::class), 'App:user_welcome');
        $this->assertSame('@App/notification/user_welcome/email.html.twig', $this->renderer->getTemplateName('email', $notification));

        $notification = new Notification($this->createMock(RecipientInterface::class), 'OtherBundle:user_welcome');
        $this->assertSame('@Other/notification/user_welcome/email.html.twig', $this->renderer->getTemplateName('email', $notification));

    }
}
