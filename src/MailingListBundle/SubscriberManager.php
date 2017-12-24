<?php

namespace Perform\MailingListBundle;

use Doctrine\ORM\EntityManagerInterface;
use Perform\MailingListBundle\Connector\ConnectorInterface;
use Perform\MailingListBundle\Enricher\EnricherInterface;
use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\Exception\ConnectorNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberManager
{
    protected $em;
    protected $connectors = [];
    protected $enrichers = [];
    protected $logger;
    protected $signups = [];

    /**
     * @param EntityManagerInterface $em
     * @param ConnectorInterface[]   $connectors
     * @param EnricherInterface[]    $enrichers
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $em, array $connectors, array $enrichers, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->connectors = $connectors;
        $this->enrichers = $enrichers;
        $this->logger = $logger;
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
            foreach ($this->enrichers as $enricher) {
                $enricher->enrich($this->signups);
            }

            foreach ($this->signups as $subscriber) {
                $this->getConnector($subscriber->getConnectorName())->subscribe($subscriber);
                $this->em->remove($subscriber);
                $this->logger->info(
                    sprintf('Created new subscriber "%s" with connector "%s".',
                            $subscriber->getEmail(),
                            $subscriber->getConnectorName()),
                    [
                        'email' => $subscriber->getEmail(),
                        'list' => $subscriber->getList(),
                        'connector' => $subscriber->getConnectorName(),
                        'attributes' => $subscriber->getAttributes(),
                    ]);
            }
            $this->em->flush();
            $this->signups = [];
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    public function processQueue($batchSize)
    {
        $repo = $this->em->getRepository('PerformMailingListBundle:Subscriber');
        while (true) {
            $this->signups = $repo->findBy([], [], $batchSize);
            if (empty($this->signups)) {
                return;
            }
            $this->flush();
        }
    }
}
