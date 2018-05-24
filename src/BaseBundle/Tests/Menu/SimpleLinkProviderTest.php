<?php

namespace Perform\BaseBundle\Tests\Menu;

use Perform\BaseBundle\Menu\SimpleLinkProvider;
use Knp\Menu\ItemInterface;

/**
 * SimpleLinkProviderTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleLinkProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function create($alias, $crud = null, $route = null, $icon = null)
    {
        return new SimpleLinkProvider($alias, $crud, $route, $icon);
    }

    protected function stubMenu()
    {
        return $this->getMock(ItemInterface::class);
    }

    public function testRouteOnly()
    {
        $provider = $this->create('foo', null, 'foo_route');

        $menu = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['route' => 'foo_route']);
        $provider->addLinks($menu);
    }

    public function testRouteWithIcon()
    {
        $provider = $this->create('foo', null, 'foo_route', 'foo');

        $menu = $this->stubMenu();
        $child = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['route' => 'foo_route'])
            ->will($this->returnValue($child));
        $child->expects($this->once())
            ->method('setExtra')
            ->with('icon', 'foo');

        $provider->addLinks($menu);
    }

    public function testCrudOnly()
    {
        $provider = $this->create('foo', 'some_crud', null);

        $menu = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['crud' => 'some_crud']);
        $provider->addLinks($menu);
    }

    public function testCrudWithIcon()
    {
        $provider = $this->create('foo', 'some_crud', null, 'foo');

        $menu = $this->stubMenu();
        $child = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['crud' => 'some_crud'])
            ->will($this->returnValue($child));
        $child->expects($this->once())
            ->method('setExtra')
            ->with('icon', 'foo');

        $provider->addLinks($menu);
    }

    public function testCrudOverridesRoute()
    {
        $provider = $this->create('foo', 'some_crud', 'foo_crud');

        $menu = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['crud' => 'some_crud']);
        $provider->addLinks($menu);
    }

    public function testRouteOrCrudIsRequired()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->create('foo');
    }

}
