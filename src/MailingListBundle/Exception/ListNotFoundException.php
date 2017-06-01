<?php

namespace Perform\MailingListBundle\Exception;

/**
 * ListNotFoundException
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ListNotFoundException extends \Exception
{
    public function __construct($listId, $providerClass)
    {
        parent::__construct(sprintf('List "%s" was not found by provider "%s"', $listId, $providerClass));
    }
}
