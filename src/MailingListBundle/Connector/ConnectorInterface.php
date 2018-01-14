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
     * This method should be safe to call multiple times with the same
     * subscriber.
     */
    public function subscribe(Subscriber $subscriber);
}
