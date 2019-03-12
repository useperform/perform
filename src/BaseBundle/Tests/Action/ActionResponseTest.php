<?php

namespace Perform\BaseBundle\Tests\Action;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Action\ActionResponse;

/**
 * ActionResponseTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionResponseTest extends TestCase
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

    public function testGetUrl()
    {
        $r = new ActionResponse('Message');
        $r->setRedirectUrl('https://example.com');
        $this->assertSame('https://example.com', $r->getUrl());
    }

    public function testRedirectListContextWithParams()
    {
        $r = new ActionResponse('Message');
        $r->setRedirect(ActionResponse::REDIRECT_LIST_CONTEXT, ['params' => ['page' => 2]]);
        $this->assertSame(['page' => 2], $r->getRouteParams());
    }
}
