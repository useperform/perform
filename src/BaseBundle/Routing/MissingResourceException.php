<?php

namespace Perform\BaseBundle\Routing;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Thrown when a required routing resource has not been imported.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MissingResourceException extends \Exception
{
    public static function create(RouteNotFoundException $e, $missingResource, $whyNeeded, $routeName)
    {
        return new self(sprintf('You must include "%s" %s. The "%s" route does not exist.', $missingResource, $whyNeeded, $routeName), 1, $e);
    }
}
