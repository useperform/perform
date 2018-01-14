<?php

namespace Perform\MailingListBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Perform\MailingListBundle\Connector\ConnectorInterface;
use Perform\MailingListBundle\Enricher\EnricherInterface;
use Perform\MailingListBundle\SubscriberManager;
use Perform\MailingListBundle\Entity\Subscriber;
use Psr\Log\LoggerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $connector1;
    protected $connector2;
    protected $enricher1;
    protected $enricher2;
    protected $logger;
    protected $manager;

    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->connector1 = $this->getMock(ConnectorInterface::class);
        $this->connector2 = $this->getMock(ConnectorInterface::class);
        $this->enricher1 = $this->getMock(EnricherInterface::class);
        $this->enricher2 = $this->getMock(EnricherInterface::class);
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->manager = new SubscriberManager($this->em, [
            'one' => $this->connector1,
            'two' => $this->connector2,
        ], [$this->enricher1, $this->enricher2], $this->logger);
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

    public function testNewSubscriberIsGivenDefaultConnectorName()
    {
        $sub = new Subscriber();
        $this->manager->addSubscriber($sub);

        $this->assertSame('one', $sub->getConnectorName());
    }

    public function testNewSubscriberConenctorNameIsNotChanged()
    {
        $sub = new Subscriber();
        $sub->setConnectorName('test');
        $this->manager->addSubscriber($sub);

        $this->assertSame('test', $sub->getConnectorName());
    }

    public function testNewSubscribersAreGivenToConnectorOnFlush()
    {
        $sub1 = new Subscriber();
        $sub2 = new Subscriber();
        $this->manager->addSubscriber($sub1);
        $this->manager->addSubscriber($sub2);

        $this->connector1->expects($this->exactly(2))
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

    public function testEnrichersAreCalled()
    {
        $one = new Subscriber();
        $two = new Subscriber();
        $three = new Subscriber();
        $this->manager->addSubscriber($one);
        $this->manager->addSubscriber($two);
        $this->manager->addSubscriber($three);
        $signups = [$one, $two, $three];
        $this->enricher1->expects($this->once())
            ->method('enrich')
            ->with($signups);
        $this->enricher2->expects($this->once())
            ->method('enrich')
            ->with($signups);

        $this->manager->flush();
    }
}
