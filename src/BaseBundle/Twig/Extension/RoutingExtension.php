<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\RequestInfo;
use Perform\BaseBundle\Routing\RouteChecker;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RoutingExtension extends \Twig_Extension
{
    protected $requestInfo;
    protected $routeChecker;

    public function __construct(RequestInfo $requestInfo, RouteChecker $routeChecker)
    {
        $this->requestInfo = $requestInfo;
        $this->routeChecker = $routeChecker;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_referer', [$this->requestInfo, 'getReferer']),
            new \Twig_SimpleFunction('perform_route_exists', [$this->routeChecker, 'routeExists']),
        ];
    }
}
