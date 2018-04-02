<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoopableServiceLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaces()
    {
        $this->assertInstanceOf(\IteratorAggregate::class, new LoopableServiceLocator([]));
        $this->assertInstanceOf(ServiceLocator::class, new LoopableServiceLocator([]));
    }

    public function testGetNames()
    {
        $locator = new LoopableServiceLocator([
            'one' => function () {return 1; },
            'two' => function () {return 2; },
        ]);

        $this->assertSame(['one', 'two'], $locator->getNames());
    }

    public function testLoop()
    {
        $locator = new LoopableServiceLocator([
            'one' => function () {return 1; },
            'two' => function () {return 2; },
        ]);
        $expected = [
            'one' => 1,
            'two' => 2,
        ];
        $this->assertSame($expected, iterator_to_array($locator->getIterator()));
    }

    public function testCreateDefinition()
    {
        $factories = [
            'service' => function() { return true; },
        ];
        $def = LoopableServiceLocator::createDefinition($factories);
        $this->assertInstanceOf(Definition::class, $def);
        $this->assertSame($factories, $def->getArgument(0));
    }
}
