<?php

namespace Perform\NotificationBundle\Tests\Notifier;

use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Notifier\TraceableNotifier;

/**
 * TraceableNotifierTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TraceableNotifierTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->notifier = new TraceableNotifier();
        $this->publisher = $this->getMock(PublisherInterface::class);
        $this->publisher->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testPublisher'));

        $this->notifier->addPublisher($this->publisher);
    }

    public function testGetPublishers()
    {
        $this->assertSame(['testPublisher' => $this->publisher], $this->notifier->getPublishers());
    }
}
