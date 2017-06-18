<?php

namespace Perform\BaseBundle\Tests\Admin;

use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Admin\AdminRequest;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * AdminRequestTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminRequestTest extends \PHPUnit_Framework_TestCase
{
    public function newRequest($query = [], $context = TypeConfig::CONTEXT_LIST)
    {
        $request = new Request($query);

        return new AdminRequest($request, $context);
    }

    public function testGetRequest()
    {
        $r = new Request();
        $req = new AdminRequest($r, TypeConfig::CONTEXT_CREATE);

        $this->assertSame($r, $req->getRequest());
    }

    public function testGetContext()
    {
        $this->assertSame(TypeConfig::CONTEXT_VIEW, $this->newRequest([], TypeConfig::CONTEXT_VIEW)->getContext());
    }

    public function testGetEntity()
    {
        $r = new Request();
        $r->attributes->set('_entity', 'FooBundle:Foo');
        $req = new AdminRequest($r, TypeConfig::CONTEXT_CREATE);

        $this->assertSame('FooBundle:Foo', $req->getEntity());
    }

    public function testGetDefaultPage()
    {
        $req = $this->newRequest();
        $this->assertSame(1, $req->getPage());
    }

    public function testGetPage()
    {
        $req = $this->newRequest(['page' => "2"]);
        $this->assertSame(2, $req->getPage());
    }

    public function testGetSortField()
    {
        $req = $this->newRequest(['sort' => 'title']);
        $this->assertSame('title', $req->getSortField());
    }

    public function testGetDefaultSortDirection()
    {
        $req = $this->newRequest();
        $this->assertSame('ASC', $req->getSortDirection());
    }

    public function testGetSortDirection()
    {
        $req = $this->newRequest(['direction' => 'desc']);
        $this->assertSame('DESC', $req->getSortDirection());
        $req = $this->newRequest(['direction' => 'n']);
        $this->assertSame('N', $req->getSortDirection());
    }

    public function testBadSortDirectionDefault()
    {
        $req = $this->newRequest(['direction' => 'foo']);
        $this->assertSame('ASC', $req->getSortDirection());
    }

    public function testGetFilter()
    {
        $req = $this->newRequest(['filter' => 'new']);
        $this->assertSame('new', $req->getFilter());
    }
}
