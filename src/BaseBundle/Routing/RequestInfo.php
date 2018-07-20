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

    /**
     * Get the referer for the current request, falling back to a
     * default if none is present.
     *
     * To prevent a loop, the fallback will be used if the referer
     * matches the URL of the current request.
     */
    public function getReferer($fallback = '/')
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request->headers->has('referer')) {
            return $fallback;
        }

        $prev = $request->headers->get('referer');
        $current = $request->getUri();

        if (trim($current) === trim($prev)) {
            return $fallback;
        }

        return $prev;
    }
}
