<?php

namespace Perform\MailingListBundle\Exception;

/**
 * Thrown when a mailing list is not found for the given connector.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ListNotFoundException extends \Exception
{
    public function __construct($listId, $connectorClass)
    {
        parent::__construct(sprintf('Mailing list "%s" was not found by connector "%s"', $listId, $connectorClass));
    }
}
