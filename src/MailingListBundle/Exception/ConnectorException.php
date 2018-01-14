<?php

namespace Perform\MailingListBundle\Exception;

/**
 * Thrown when a connector fails for whatever reason.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConnectorException extends \Exception
{
    public function __construct($message, $connectorClass)
    {
        parent::__construct(sprintf('Connector "%s" encountered an error: %s', $connectorClass, $message));
    }
}
