<?php

namespace Perform\MailingListBundle\Connector;

use Perform\MailingListBundle\Entity\Subscriber;
use DrewM\MailChimp\MailChimp;
use Psr\Log\LoggerInterface;

/**
 * Add subscribers to a MailChimp list.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MailChimpConnector implements ConnectorInterface
{
    protected $mailChimp;
    protected $logger;

    public function __construct(MailChimp $mailChimp, LoggerInterface $logger)
    {
        $this->mailChimp = $mailChimp;
        $this->logger = $logger;
    }

    public function subscribe(Subscriber $subscriber)
    {
        $url = "lists/{$subscriber->getList()}/members";
        $params = [
            'email_address' => $subscriber->getEmail(),
            'status' => 'subscribed',
        ];
        $this->mailChimp->post($url, $params);
        $this->logger->debug('MailChimp: POST '.$url, $params);
    }
}
