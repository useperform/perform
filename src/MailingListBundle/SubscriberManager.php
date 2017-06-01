<?php

namespace Perform\MailingListBundle;

use Doctrine\ORM\EntityManagerInterface;
use Perform\MailingListBundle\Connector\ConnectorInterface;
use Perform\MailingListBundle\Entity\Subscriber;

/**
 * SubscriberManager
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberManager
{
    protected $em;
    protected $connector;
    protected $signups = [];

    public function __construct(EntityManagerInterface $em, ConnectorInterface $connector)
    {
        $this->em = $em;
        $this->connector = $connector;
    }

    public function addSubscriber(Subscriber $subscriber)
    {
        $this->em->persist($subscriber);
        $this->em->flush();

        $this->signups[] = $subscriber;
    }

    public function flush()
    {
        foreach ($this->signups as $subscriber) {
            $this->connector->subscribe($subscriber);
            $this->em->remove($subscriber);
        }
        $this->em->flush();
    }

    public function onKernelTerminate($event)
    {
        $this->flush();
    }
}
