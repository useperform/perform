<?php

namespace Perform\MailingListBundle\Connector;

use Perform\MailingListBundle\Entity\Subscriber;

/**
 * Connectors take new subscribers from the queue add them to a
 * mailing list.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ConnectorInterface
{
    /**
     * Add a subscriber to a mailing list.
     *
     * This method is safe to call multiple times with the same signup
     * without being subscribed multiple times.
     */
    public function subscribe(Subscriber $subscriber);
}
