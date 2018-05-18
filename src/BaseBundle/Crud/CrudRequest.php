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
    protected $context;
    protected $entityClass;
    protected $page = 1;
    protected $sortField;
    protected $sortDirection;
    protected $filter;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public static function fromRequest(Request $request, $context)
    {
        $req = new static($context);
        $req->setEntityClass($request->attributes->get('_entity'));
        $req->setPage($request->query->get('page', 1));
        $req->setSortField($request->query->get('sort'));
        $req->setSortDirection($request->query->get('direction'));
        $req->setFilter($request->query->get('filter'));

        return $req;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set the entity class for this request.
     *
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the entity class for this request.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the page number for this request, only relevant on the list context.
     *
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = (int) $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;

        return $this;
    }

    /**
     * @return string|null
     *
     * @param string $default The default field to return if none is set
     */
    public function getSortField($default = null)
    {
        return $this->sortField ?: $default;
    }

    /**
     * @param string $sortDirection
     */
    public function setSortDirection($sortDirection)
    {
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'DESC' && $sortDirection !== 'N') {
            $sortDirection = 'ASC';
        }
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @return string $default The default direction to return if none is set
     */
    public function getSortDirection($default = 'ASC')
    {
        return $this->sortDirection ?: $default;
    }

    /**
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return string $default The default direction to return if none is set
     */
    public function getFilter($default = null)
    {
        return $this->filter ?: $default;
    }
}
