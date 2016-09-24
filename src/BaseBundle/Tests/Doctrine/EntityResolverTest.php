<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Doctrine\EntityResolver;

/**
 * EntityResolverTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $extendedAliases = [
            'PerformBaseBundle:Foo' => 'TestBundle:Foo',
        ];
        $extended = [
            'Perform\BaseBundle\Entity\Foo' => 'TestBundle\Entity\Foo',
        ];
        $resolver = new EntityResolver($extendedAliases, $extended);
        $this->assertSame('TestBundle:Foo', $resolver->resolve('PerformBaseBundle:Foo'));
        $this->assertSame('TestBundle\Entity\Foo', $resolver->resolve('Perform\BaseBundle\Entity\Foo'));
    }

    public function testResolveReturnsSameNameForUnknown()
    {
        $resolver = new EntityResolver();
        $this->assertSame('PerformBaseBundle:Bar', $resolver->resolve('PerformBaseBundle:Bar'));
        $this->assertSame('Perform\BaseBundle\Entity\Bar', $resolver->resolve('Perform\BaseBundle\Entity\Bar'));
    }
}
