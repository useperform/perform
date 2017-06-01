<?php

namespace Perform\MailingListBundle\Connector;

use Doctrine\ORM\EntityManagerInterface;
use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\Exception\ListNotFoundException;
use Perform\MailingListBundle\Entity\LocalSubscriber;
use Perform\MailingListBundle\SubscriberFields;

/**
 * Simple connector that stores subscribers locally.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalConnector implements ConnectorInterface
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function subscribe(Subscriber $subscriber)
    {
        $listRepo = $this->em->getRepository('PerformMailingListBundle:LocalList');
        $list = $listRepo->findOneBy(['slug' => $subscriber->getList()]);

        if (!$list) {
            throw new ListNotFoundException($subscriber->getList(), __CLASS__);
        }

        $subRepo = $this->em->getRepository('PerformMailingListBundle:LocalSubscriber');
        $existing = $subRepo->findOneBy(['email' => $subscriber->getEmail(), 'list' => $list]);
        if ($existing) {
            return;
        }

        $localSub = new LocalSubscriber();
        $localSub->setEmail($subscriber->getEmail());
        $localSub->setList($list);
        $localSub->setForename($subscriber->getAttribute(SubscriberFields::FIRST_NAME));

        $this->em->persist($localSub);
        $this->em->flush();
    }
}
