<?php

namespace Perform\BaseBundle\Tests\Crud;

use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Tests\Fixtures\ReflectionUtil\ChildEntity;
use Perform\BaseBundle\Tests\Fixtures\ReflectionUtil\ParentEntity;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContext()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_VIEW);
        $this->assertSame(CrudRequest::CONTEXT_VIEW, $req->getContext());
    }

    public function testGetCrudName()
    {

        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_VIEW);
        $this->assertSame('some_crud', $req->getCrudName());
    }

    public function testGetSetPage()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setPage(2));
        $this->assertSame(2, $req->getPage());
    }

    public function testPageHasADefault()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame(1, $req->getPage());
    }

    public function testGetSetSortField()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setSortField('title'));
        $this->assertSame('title', $req->getSortField());
    }

    public function testSetDefaultSortField()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setDefaultSortField('id'));
        $this->assertSame('id', $req->getSortField());

        $this->assertSame($req, $req->setSortField('title'));
        $this->assertSame('title', $req->getSortField());

        $this->assertSame($req, $req->setDefaultSortField('id'));
        $this->assertSame('title', $req->getSortField());
    }

    public function testGetSetSortDirection()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setSortDirection('desc'));
        $this->assertSame('DESC', $req->getSortDirection());

        $this->assertSame($req, $req->setSortDirection('n'));
        $this->assertSame('N', $req->getSortDirection());
    }

    public function testSortDirectionHasADefault()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame('ASC', $req->getSortDirection());
    }

    public function testSetDefaultSortDirection()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setDefaultSortDirection('desc'));
        $this->assertSame('DESC', $req->getSortDirection());

        $this->assertSame($req, $req->setSortDirection('ASC'));
        $this->assertSame('ASC', $req->getSortDirection());

        $this->assertSame($req, $req->setDefaultSortDirection('desc'));
        $this->assertSame('ASC', $req->getSortDirection());
    }

    public function testBadSortDirection()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setSortDirection('foo'));
        $this->assertSame('ASC', $req->getSortDirection());
    }

    public function testGetSetFilter()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setFilter('new'));
        $this->assertSame('new', $req->getFilter());
    }

    public function testSetDefaultFilter()
    {
        $req = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->assertSame($req, $req->setDefaultFilter('new'));
        $this->assertSame('new', $req->getFilter());

        $this->assertSame($req, $req->setFilter('old'));
        $this->assertSame('old', $req->getFilter());

        $this->assertSame($req, $req->setDefaultFilter('new'));
        $this->assertSame('old', $req->getFilter());
    }

    public function testFromRequest()
    {
        $request = new Request([
            'page' => 2,
            'sort' => 'title',
            'direction' => 'DESC',
            'filter' => 'some_filter',
        ]);
        $request->attributes->set('_crud', 'some_crud');
        $req = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_LIST);

        $this->assertSame(CrudRequest::CONTEXT_LIST, $req->getContext());
        $this->assertSame('some_crud', $req->getCrudName());
        $this->assertSame(2, $req->getPage());
        $this->assertSame('title', $req->getSortField());
        $this->assertSame('DESC', $req->getSortDirection());
        $this->assertSame('some_filter', $req->getFilter());
    }
}
