<?php

namespace Perform\BaseBundle\Tests\Action;

use Perform\BaseBundle\Action\ActionResponse;

/**
 * ActionResponseTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $r = new ActionResponse('Some success message');
        $this->assertSame('Some success message', $r->getMessage());
    }

    public function testGetNoRoute()
    {
        $r = new ActionResponse('Message');
        $this->assertNull($r->getRoute());
        $this->assertSame([], $r->getRouteParams());
    }

    public function testGetRouteNoParams()
    {
        $r = new ActionResponse('Message');
        $r->setRedirectRoute('some_route');
        $this->assertSame('some_route', $r->getRoute());
        $this->assertSame([], $r->getRouteParams());
    }

    public function testGetRouteWithParams()
    {
        $r = new ActionResponse('Message');
        $r->setRedirectRoute('some_route', ['param' => 'foo']);
        $this->assertSame('some_route', $r->getRoute());
        $this->assertSame(['param' => 'foo'], $r->getRouteParams());
    }
}
