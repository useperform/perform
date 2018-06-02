<?php

namespace Perform\BaseBundle\Action;

/**
 * ActionResponse.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionResponse
{
    const REDIRECT_NONE = 'none';
    const REDIRECT_URL = 'url';
    const REDIRECT_ROUTE = 'route';
    const REDIRECT_PREVIOUS = 'previous';
    const REDIRECT_CURRENT = 'current';
    const REDIRECT_LIST_CONTEXT = 'list';

    protected $redirect = 'none';
    protected $message;
    protected $url;
    protected $route;
    protected $routeParams = [];

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $redirect
     *
     * @return ActionResponse
     */
    public function setRedirect($redirect, array $options = [])
    {
        $this->redirect = $redirect;

        if ($redirect === static::REDIRECT_URL) {
            if (!isset($options['url'])) {
                throw new \InvalidArgumentException('REDIRECT_URL must have the "url" option supplied.');
            }
            $this->url = $options['url'];
        }

        if ($redirect === static::REDIRECT_ROUTE) {
            if (!isset($options['route'])) {
                throw new \InvalidArgumentException('REDIRECT_ROUTE must have the "route" option supplied.');
            }

            $this->route = $options['route'];
        }

        if ($redirect === static::REDIRECT_ROUTE || $redirect === static::REDIRECT_LIST_CONTEXT) {
            $this->routeParams = isset($options['params']) ? (array) $options['params'] : [];
        }

        return $this;
    }

    /**
     * Shortcut for calling setRedirect with REDIRECT_ROUTE.
     *
     * @param string $route
     * @param array  $params
     *
     * @return ActionResponse
     */
    public function setRedirectRoute($route, array $params = [])
    {
        return $this->setRedirect(static::REDIRECT_ROUTE, ['route' => $route, 'params' => $params]);
    }

    /**
     * Shortcut for calling setRedirect with REDIRECT_URL.
     *
     * @param string $url
     *
     * @return ActionResponse
     */
    public function setRedirectUrl($url)
    {
        return $this->setRedirect(static::REDIRECT_URL, ['url' => $url]);
    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }
}
