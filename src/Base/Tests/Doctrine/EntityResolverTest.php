<?php

namespace Admin\Base\Tests\Doctrine;

use Admin\Base\Doctrine\EntityResolver;

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
            'AdminBaseBundle:Foo' => 'TestBundle:Foo',
        ];
        $extended = [
            'Admin\BaseBundle\Entity\Foo' => 'TestBundle\Entity\Foo',
        ];
        $resolver = new EntityResolver($extendedAliases, $extended);
        $this->assertSame('TestBundle:Foo', $resolver->resolve('AdminBaseBundle:Foo'));
        $this->assertSame('TestBundle\Entity\Foo', $resolver->resolve('Admin\BaseBundle\Entity\Foo'));
    }

    public function testResolveReturnsSameNameForUnknown()
    {
        $resolver = new EntityResolver();
        $this->assertSame('AdminBaseBundle:Bar', $resolver->resolve('AdminBaseBundle:Bar'));
        $this->assertSame('Admin\BaseBundle\Entity\Bar', $resolver->resolve('Admin\BaseBundle\Entity\Bar'));
    }
}
