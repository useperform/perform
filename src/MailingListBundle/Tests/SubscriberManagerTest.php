<?php

namespace Perform\MailingListBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Perform\MailingListBundle\Connector\ConnectorInterface;
use Perform\MailingListBundle\SubscriberManager;
use Perform\MailingListBundle\Entity\Subscriber;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $connector;
    protected $manager;

    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->connector = $this->getMock(ConnectorInterface::class);
        $this->manager = new SubscriberManager($this->em, $this->connector);
    }

    public function testNewSubscriberIsSaved()
    {
        $sub = new Subscriber();
        $this->em->expects($this->once())
            ->method('persist')
            ->with($sub);
        $this->em->expects($this->once())
            ->method('flush');

        $this->manager->addSubscriber($sub);
    }

    public function testNewSubscribersAreGivenToConnectorOnFlush()
    {
        $sub1 = new Subscriber();
        $sub2 = new Subscriber();
        $this->manager->addSubscriber($sub1);
        $this->manager->addSubscriber($sub2);

        $this->connector->expects($this->exactly(2))
            ->method('subscribe')
            ->withConsecutive(
                [$this->equalTo($sub1)],
                [$this->equalTo($sub2)]
            );
        $this->em->expects($this->exactly(2))
            ->method('remove')
            ->withConsecutive(
                [$this->equalTo($sub1)],
                [$this->equalTo($sub2)]
            );
        $this->em->expects($this->once())
            ->method('flush');

        $this->manager->flush();

        //test flush is idempotent
        $this->manager->flush();
    }

    public function testFlushDoesNothingWithNoNewSubscribers()
    {
        $this->em->expects($this->never())
            ->method('flush');

        $this->manager->flush();
    }
}
