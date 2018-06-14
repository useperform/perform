<?php

namespace Perform\BaseBundle\Tests\Menu;

use Knp\Menu\MenuItem;
use Perform\BaseBundle\Menu\MenuBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuFactory;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;
    protected $dispatcher;
    protected $builder;

    public function setUp()
    {
        $this->factory = new MenuFactory();
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->builder = new MenuBuilder($this->factory, $this->dispatcher);
    }

    public function testSortByPriority()
    {
        $menu = new MenuItem('base', $this->factory);
        $menu->addChild('one')->setExtra('priority', -10);
        $menu->addChild('two');
        $menu->addChild('three')->setExtra('priority', 40);

        $this->builder->sortByPriority($menu);
        $this->assertSame(['three', 'two', 'one'], array_keys($menu->getChildren()));
    }

    public function testSortByPriorityRecursively()
    {
        $menu = new MenuItem('', $this->factory);
        $menu->addChild('one')->setExtra('priority', -10);
        $two = $menu->addChild('two');
        $two->addChild('two.one')->setExtra('priority', 60);
        $two->addChild('two.two')->setExtra('priority', 100);
        $three = $menu->addChild('three');
        $three->setExtra('priority', 40);

        $this->builder->sortByPriority($menu);
        $this->assertSame(['three', 'two', 'one'], array_keys($menu->getChildren()));
        $this->assertSame(['two.two', 'two.one'], array_keys($menu->getChild('two')->getChildren()));
    }
}
