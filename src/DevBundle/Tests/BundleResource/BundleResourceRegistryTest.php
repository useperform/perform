<?php

namespace Perform\DevBundle\Tests\BundleResource;

use Perform\DevBundle\BundleResource\BundleResourceRegistry;
use Perform\DevBundle\BundleResource\BundleResourceInterface;

/**
 * BundleResourceRegistryTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleResourceRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->reg = new BundleResourceRegistry();
    }

    protected function resource($bundleName, array $requiredBundles = [])
    {
        $r = $this->getMock(BundleResourceInterface::class);
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
        $this->reg->addParentResource($b = $this->resource('BarBundle'));

        $this->assertSame(['BarBundle' => $b], $this->reg->getParentResources());
    }

    public function testResolveResources()
    {
        $this->reg->addResource($a = $this->resource('ABundle', ['BBundle', 'CBundle']));
        $this->reg->addResource($b = $this->resource('BBundle', ['DBundle']));
        $this->reg->addResource($c = $this->resource('CBundle', ['DBundle']));
        $this->reg->addResource($d = $this->resource('DBundle'));

        $this->assertSame([$d, $b, $c, $a], $this->reg->resolveResources(['ABundle']));
    }
}
