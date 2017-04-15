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
    protected function create($alias, $entity = null, $route = null, $icon = null)
    {
        return new SimpleLinkProvider($alias, $entity, $route, $icon);
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

    public function testEntityOnly()
    {
        $provider = $this->create('foo', 'FooBundle:Entity', null);

        $menu = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['entity' => 'FooBundle:Entity']);
        $provider->addLinks($menu);
    }

    public function testEntityWithIcon()
    {
        $provider = $this->create('foo', 'FooBundle:Entity', null, 'foo');

        $menu = $this->stubMenu();
        $child = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['entity' => 'FooBundle:Entity'])
            ->will($this->returnValue($child));
        $child->expects($this->once())
            ->method('setExtra')
            ->with('icon', 'foo');

        $provider->addLinks($menu);
    }

    public function testEntityOverridesRoute()
    {
        $provider = $this->create('foo', 'FooBundle:Entity', 'foo_entity');

        $menu = $this->stubMenu();
        $menu->expects($this->once())
            ->method('addChild')
            ->with('foo', ['entity' => 'FooBundle:Entity']);
        $provider->addLinks($menu);
    }

    public function testRouteOrEntityIsRequired()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->create('foo');
    }

}
