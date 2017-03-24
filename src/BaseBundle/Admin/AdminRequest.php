<?php

namespace Perform\BaseBundle\Admin;

use Symfony\Component\HttpFoundation\Request;

/**
 * A request that involves an admin for an entity.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminRequest
{
    protected $request;
    protected $context;

    public function __construct(Request $request, $context)
    {
        $this->request = $request;
        $this->context = $context;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getEntity()
    {
        return $this->request->attributes->get('_entity');
    }

    public function getPage()
    {
        return $this->request->query->get('page', 1);
    }

    public function getSortField($default = null)
    {
        return $this->request->query->get('sort', $default);
    }

    public function getSortDirection($default = 'ASC')
    {
        $direction = strtoupper($this->request->query->get('direction', $default));
        if ($direction !== 'DESC' && $direction !== 'N') {
            $direction = 'ASC';
        }

        return $direction;
    }

    public function getFilter($default = null)
    {
        return $this->request->query->get('filter', $default);
    }
}
