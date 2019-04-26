<?php

namespace Perform\DevBundle\Tests\BundleResource;

use PHPUnit\Framework\TestCase;
use Perform\DevBundle\BundleResource\BundleResourceRegistry;
use Perform\DevBundle\BundleResource\ResourceInterface;
use Perform\DevBundle\BundleResource\ParentResourceInterface;

/**
 * BundleResourceRegistryTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleResourceRegistryTest extends TestCase
{
    public function setUp()
    {
        $this->reg = new BundleResourceRegistry();
    }

    protected function resource($bundleName)
    {
        $r = $this->createMock(ResourceInterface::class);
        $r->expects($this->any())
            ->method('getBundleName')
            ->will($this->returnValue($bundleName));

        return $r;
    }

    protected function parentResource($bundleName, array $requiredBundles = [])
    {
        $r = $this->createMock(ParentResourceInterface::class);
        $r->expects($this->any())
            ->method('getBundleName')
            ->will($this->returnValue($bundleName));

        $r->expects($this->any())
            ->method('getRequiredBundles')
            ->will($this->returnValue($requiredBundles));

        return $r;
    }

    public function testResources()
    {
        $this->reg->addResource($a = $this->resource('FooBundle'));
        $this->reg->addResource($b = $this->resource('BarBundle'));

        $this->assertSame(['FooBundle' => $a, 'BarBundle' => $b], $this->reg->getResources());
    }

    public function testParentResources()
    {
        $this->reg->addResource($a = $this->resource('FooBundle'));
        $this->reg->addParentResource($b = $this->parentResource('BarBundle'));

        $this->assertSame(['BarBundle' => $b], $this->reg->getParentResources());
    }

    public function testResolveResources()
    {
        $this->reg->addParentResource($a = $this->parentResource('ABundle', ['BBundle', 'CBundle']));
        $this->reg->addParentResource($b = $this->parentResource('BBundle', ['DBundle']));
        $this->reg->addParentResource($c = $this->parentResource('CBundle', ['DBundle']));
        $this->reg->addResource($d = $this->resource('DBundle'));

        $this->assertSame([$d, $b, $c, $a], $this->reg->resolveResources(['ABundle']));
    }
}
