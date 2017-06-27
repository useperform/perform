<?php

namespace NotificationBundle\Tests\Publisher;

use Perform\NotificationBundle\Publisher\LocalPublisher;
use Perform\NotificationBundle\Notification;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Templating\EngineInterface;
use Perform\NotificationBundle\Recipient\RecipientInterface;

/**
 * LocalPublisherTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalPublisherTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $templating;
    protected $publisher;

    public function setUp()
    {
        $this->entityManager = $this->getMock(ObjectManager::class);
        $this->templating = $this->getMock(EngineInterface::class);
        $this->publisher = new LocalPublisher($this->entityManager, $this->templating);
    }

    protected function newNotification($type)
    {
        return new Notification($this->getMock(RecipientInterface::class), $type);
    }

    public function testSend()
    {
        $this->templating->expects($this->once())
            ->method('render')
            ->with('PerformNotificationBundle:test:local.html.twig');

        $this->publisher->send($this->newNotification('test'));
    }

    public function testSendWithNamespacing()
    {
        $this->templating->expects($this->once())
            ->method('render')
            ->with('PerformBaseBundle:notifications:crud_update/local.html.twig');

        $this->publisher->send($this->newNotification('PerformBaseBundle:crud_update'));
    }
}
