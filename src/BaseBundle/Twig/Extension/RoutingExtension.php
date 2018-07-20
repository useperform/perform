<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\RequestInfo;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RoutingExtension extends \Twig_Extension
{
    protected $requestInfo;

    public function __construct(RequestInfo $requestInfo)
    {
        $this->requestInfo = $requestInfo;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_referer', [$this->requestInfo, 'getReferer']),
        ];
    }
}
