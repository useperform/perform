<?php

namespace Perform\BaseBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Crud\CrudRequest;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class QueryEvent extends Event
{
    const LIST_QUERY = 'perform_base.crud.list_query';
    const VIEW_QUERY = 'perform_base.crud.view_query';
    const EDIT_QUERY = 'perform_base.crud.edit_query';

    protected $qb;
    protected $request;

    public function __construct(QueryBuilder $qb, CrudRequest $request)
    {
        $this->qb = $qb;
        $this->request = $request;
    }

    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    public function getQueryBuilder()
    {
        return $this->qb;
    }

    public function getCrudRequest()
    {
        return $this->request;
    }
}
