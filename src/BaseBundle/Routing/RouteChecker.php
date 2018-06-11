<?php

namespace Perform\BaseBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * Check if routes exist.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RouteChecker
{
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function routeExists($routeName)
    {
        try {
            $this->urlGenerator->generate($routeName);

            return true;
        } catch (RouteNotFoundException $e) {
            return false;
        } catch (MissingMandatoryParametersException $e) {
            // missing parameters, but route exists
            return true;
        }
    }
}
