<?php

namespace Perform\BaseBundle\Crud;

use Symfony\Component\HttpFoundation\Request;

/**
 * A request that involves a crud service.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRequest
{
    protected $request;
    protected $context;
    protected $entityOverride;

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

    /**
     * Override the entity class for this request.
     *
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entityOverride = $entity;
    }

    /**
     * Get the entity name for this request.
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entityOverride ? $this->entityOverride : $this->request->attributes->get('_entity');
    }

    public function getPage()
    {
        return (int) $this->request->query->get('page', 1);
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
