<?php

namespace Perform\BaseBundle\Action;

/**
 * Thrown when a failure occurs during ActionInterface#run() and you
 * want to pass a specific message to the user.
 *
 * If any other exception is thrown during this method, a generic
 * error message will be shown to prevent unsightly exception messages
 * being passed to the user, e.g. a database exception that include
 * SQL snippets in the message.
 *
 * If this exception is thrown, the message will be passed to the user.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionFailedException extends \Exception
{
}
