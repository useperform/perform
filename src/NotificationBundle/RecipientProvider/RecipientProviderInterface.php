<?php

namespace Perform\NotificationBundle\RecipientProvider;

/**
 * RecipientProviderInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface RecipientProviderInterface
{
    /**
     * Get an array of recipients to send notifications to, optionally
     * providing some criteria.
     *
     * @return RecipientInterface[] an array of recipients
     */
    public function getRecipients(array $criteria = []);
}
