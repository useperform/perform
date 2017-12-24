<?php

namespace Perform\MailingListBundle\Enricher;

use Perform\MailingListBundle\Entity\Subscriber;

/**
 * Enrichers are used to add attributes to incoming subscribers with
 * data they have access to.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface EnricherInterface
{
    /**
     * @param Subscriber[] $subscribers
     */
    public function enrich(array $subscribers);
}
