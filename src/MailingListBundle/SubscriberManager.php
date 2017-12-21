<?php

namespace Perform\MailingListBundle;

use Doctrine\ORM\EntityManagerInterface;
use Perform\MailingListBundle\Connector\ConnectorInterface;
use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\Exception\ConnectorNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberManager
{
    protected $em;
    protected $connector;
    protected $signups = [];

    /**
     * @var EntityManagerInterface $em
     * @var ConnectorInterface[] $connectors
     */
    public function __construct(EntityManagerInterface $em, array $connectors)
    {
        $this->em = $em;
        $this->connectors = $connectors;
    }

    /**
     * @return string
     */
    public function getDefaultConnectorName()
    {
        if (empty($this->connectors)) {
            throw new ConnectorNotFoundException('No mailing list connectors are registered.');
        }

        return array_keys($this->connectors)[0];
    }

    /**
     * Get a mailing list connector. If no name is given, return the default connector.
     *
     * @param string|null $name
     *
     * @return ConnectorInterface[]
     */
    public function getConnector($name = null)
    {
        if (!$name) {
            $name = $this->getDefaultConnectorName();
        }

        if (!isset($this->connectors[$name])) {
            throw new ConnectorNotFoundException(sprintf('The mailing list connector "%s" was not found.', $name));
        }

        return $this->connectors[$name];
    }

    public function addSubscriber(Subscriber $subscriber)
    {
        if (!$subscriber->getConnectorName()) {
            $subscriber->setConnectorName($this->getDefaultConnectorName());
        }

        $this->em->persist($subscriber);
        $this->em->flush();

        $this->signups[] = $subscriber;
    }

    public function flush()
    {
        if (empty($this->signups)) {
            return;
        }

        $this->em->beginTransaction();
        try {
            foreach ($this->signups as $subscriber) {
                $this->getConnector($subscriber->getConnectorName())->subscribe($subscriber);
                $this->em->remove($subscriber);
            }
            $this->em->flush();
            $this->signups = [];
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
