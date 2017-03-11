<?php

namespace Perform\BaseBundle\Action;

/**
 * ActionResponse.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionResponse
{
    protected $message;
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
     * @param string $route
     * @param array  $params
     *
     * @return ActionResponse
     */
    public function setRedirectRoute($route, array $params = [])
    {
        $this->route = $route;
        $this->routeParams = $params;

        return $this;
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
