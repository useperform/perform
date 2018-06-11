<?php

namespace Perform\BaseBundle\Tests\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Perform\BaseBundle\EventListener\SimpleMenuListener;
use Perform\BaseBundle\Event\MenuEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleMenuListenerTest extends \PHPUnit_Framework_TestCase
{
    protected function create($name, $crud = null, $route = null, $icon = null, $priority = 0)
    {
        return new SimpleMenuListener($name, $crud, $route, $icon, $priority);
    }

    protected function stubEvent()
    {
        return new MenuEvent('perform_sidebar', $this->getMock(ItemInterface::class), $this->getMock(FactoryInterface::class));
    }

    public function testRouteOnly()
    {
        $listener = $this->create('foo', null, 'foo_route');

        $event = $this->stubEvent();
        $event->getMenu()->expects($this->once())
            ->method('addChild')
            ->with('foo', ['route' => 'foo_route']);

        $listener->onMenuBuild($event);
    }

    public function testRouteWithIcon()
    {
        $listener = $this->create('foo', null, 'foo_route', 'foo');

        $event = $this->stubEvent();
        $child = $this->stubEvent()->getMenu();
        $event->getMenu()->expects($this->once())
            ->method('addChild')
            ->with('foo', ['route' => 'foo_route'])
            ->will($this->returnValue($child));
        $child->expects($this->once())
            ->method('setExtra')
            ->with('icon', 'foo');

        $listener->onMenuBuild($event);
    }

    public function testCrudOnly()
    {
        $listener = $this->create('foo', 'some_crud', null);

        $event = $this->stubEvent();
        $event->getMenu()->expects($this->once())
            ->method('addChild')
            ->with('foo', ['crud' => 'some_crud']);

        $listener->onMenuBuild($event);
    }

    public function testCrudWithIcon()
    {
        $listener = $this->create('foo', 'some_crud', null, 'foo');

        $event = $this->stubEvent();
        $child = $this->stubEvent()->getMenu();
        $event->getMenu()->expects($this->once())
            ->method('addChild')
            ->with('foo', ['crud' => 'some_crud'])
            ->will($this->returnValue($child));
        $child->expects($this->once())
            ->method('setExtra')
            ->with('icon', 'foo');

        $listener->onMenuBuild($event);
    }

    public function testCrudOverridesRoute()
    {
        $listener = $this->create('foo', 'some_crud', 'foo_crud');

        $event = $this->stubEvent();
        $event->getMenu()->expects($this->once())
            ->method('addChild')
            ->with('foo', ['crud' => 'some_crud']);

        $listener->onMenuBuild($event);
    }

    public function testRouteOrCrudIsRequired()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->create('foo');
    }

    public function testCrudWithIconAndPriority()
    {
        $listener = $this->create('foo', 'some_crud', null, 'foo', 3000);

        $event = $this->stubEvent();
        $child = $this->stubEvent()->getMenu();
        $event->getMenu()->expects($this->once())
            ->method('addChild')
            ->with('foo', ['crud' => 'some_crud'])
            ->will($this->returnValue($child));
        $child->expects($this->exactly(2))
            ->method('setExtra')
            ->withConsecutive(
                ['icon', 'foo'],
                ['priority', 3000]
            );

        $listener->onMenuBuild($event);
    }
}
