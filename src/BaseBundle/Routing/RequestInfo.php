<?php

namespace Perform\BaseBundle\Routing;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Get routing information about the current request.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RequestInfo
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getReferer($fallback = '/')
    {
        $headers = $this->requestStack->getCurrentRequest()->headers;

        return $headers->has('referer') ? $headers->get('referer') : $fallback;
    }
}
