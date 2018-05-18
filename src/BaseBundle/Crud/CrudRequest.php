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
    const CONTEXT_LIST = 'list';
    const CONTEXT_VIEW = 'view';
    const CONTEXT_CREATE = 'create';
    const CONTEXT_EDIT = 'edit';
    const CONTEXT_EXPORT = 'export';

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
     * Check if the given entity class is supported by this request.
     * A supported class is either the same as, or is a parent of, the current entity class.
     *
     * @param string
     *
     * @return bool
     */
    public function supportsEntityClass($entityClass)
    {
        return $this->entityClass === $entityClass || is_subclass_of($this->entityClass, $entityClass);
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
     * @param string $sortField
     */
    public function setDefaultSortField($sortField)
    {
        if (!$this->sortField) {
            $this->sortField = $sortField;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param string $sortDirection
     */
    public function setSortDirection($sortDirection)
    {
        $sortDirection = strtoupper($sortDirection);
        if (in_array($sortDirection, ['ASC', 'DESC', 'N'])) {
            $this->sortDirection = $sortDirection;
        }

        return $this;
    }

    /**
     * @param string $sortDirection
     */
    public function setDefaultSortDirection($sortDirection)
    {
        if (!$this->sortDirection) {
            return $this->setSortDirection($sortDirection);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection ?: 'ASC';
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
     * @param string $filter
     */
    public function setDefaultFilter($filter)
    {
        if (!$this->filter) {
            $this->filter = $filter;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
