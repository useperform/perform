<?php

namespace NotificationBundle\Tests\Publisher;

use Admin\NotificationBundle\Publisher\LocalPublisher;
use Admin\NotificationBundle\Notification;

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
        $this->entityManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->templating = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $this->publisher = new LocalPublisher($this->entityManager, $this->templating);
    }

    protected function newNotification($type)
    {
        return new Notification($this->getMock('Admin\NotificationBundle\RecipientInterface'), $type);
    }

    public function testSend()
    {
        $this->templating->expects($this->once())
            ->method('render')
            ->with('AdminNotificationBundle:test:local.html.twig');

        $this->publisher->send($this->newNotification('test'));
    }

    public function testSendWithNamespacing()
    {
        $this->templating->expects($this->once())
            ->method('render')
            ->with('AdminBaseBundle:notifications:crud_update/local.html.twig');

        $this->publisher->send($this->newNotification('AdminBaseBundle:crud_update'));
    }
}
