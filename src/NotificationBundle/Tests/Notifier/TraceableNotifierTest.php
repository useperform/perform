<?php

namespace Perform\NotificationBundle\Tests\Notifier;

use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Notifier\TraceableNotifier;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\NotificationBundle\Notifier\NotifierInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TraceableNotifierTest extends \PHPUnit_Framework_TestCase
{
    protected $publisher;
    protected $notifier;

    public function setUp()
    {
        $this->publisher = $this->getMock(PublisherInterface::class);
        $locator = new LoopableServiceLocator([
            'testPublisher' => function() { return $this->publisher; }
        ]);

        $this->notifier = new TraceableNotifier($locator);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(NotifierInterface::class, $this->notifier);
    }

    public function testGetPublishers()
    {
        $this->assertSame(['testPublisher' => get_class($this->publisher)], $this->notifier->getPublisherClasses());
    }
}
