<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Doctrine\Common\Proxy\ProxyGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Temping\Temping;

/**
 * EntityResolverTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $temp;
    protected $proxyGenerator;

    public function setUp()
    {
        $this->temp = new Temping();
        $this->proxyGenerator = new ProxyGenerator($this->temp->getDirectory().'proxy', __NAMESPACE__.'\\Proxy');
    }

    public function tearDown()
    {
        $this->temp->reset();
    }

    public function testResolve()
    {
        $aliases = [
            'PerformBaseBundle:Foo' => 'Perform\BaseBundle\Entity\Foo',
            'TestBundle:Foo' => 'Perform\BaseBundle\Entity\Foo',
        ];
        $extended = [
            'Perform\BaseBundle\Entity\Foo' => 'TestBundle\Entity\Foo',
        ];
        $resolver = new EntityResolver($aliases, $extended);
        $this->assertSame('TestBundle\Entity\Foo', $resolver->resolve('TestBundle:Foo'));
        $this->assertSame('TestBundle\Entity\Foo', $resolver->resolve('PerformBaseBundle:Foo'));
        $this->assertSame('TestBundle\Entity\Foo', $resolver->resolve('Perform\BaseBundle\Entity\Foo'));
    }

    public function testResolveReturnsSameNameForUnknown()
    {
        $resolver = new EntityResolver();
        $this->assertSame('PerformBaseBundle:Bar', $resolver->resolve('PerformBaseBundle:Bar'));
        $this->assertSame('Perform\BaseBundle\Entity\Bar', $resolver->resolve('Perform\BaseBundle\Entity\Bar'));
    }

    public function testResolveObject()
    {
        $resolver = new EntityResolver();

        $this->assertSame(\stdClass::class, $resolver->resolve(new \stdClass()));
    }

    public function testResolveExtendedObject()
    {
        $resolver = new EntityResolver([], [
            \stdClass::class => 'SomeBundle\Entity\Extended',
        ]);

        $this->assertSame('SomeBundle\Entity\Extended', $resolver->resolve(new \stdClass()));
    }

    public function testResolveInvalidType()
    {
        $resolver = new EntityResolver();
        $this->setExpectedException(\InvalidArgumentException::class);
        $resolver->resolve([]);
    }

    public function testResolveNoExtend()
    {
        $resolver = new EntityResolver([], [
            \stdClass::class => 'SomeBundle\Entity\Extended',
        ]);

        $this->assertSame(\stdClass::class, $resolver->resolveNoExtend(new \stdClass()));
    }

    protected function generateProxyClass($class)
    {
        $proxyClass = __NAMESPACE__.'\\Proxy\\__CG__\\'.$class;

        if (class_exists($proxyClass, false)) {
            return $proxyClass;
        }

        $metadata = $this->getMockBuilder(ClassMetadata::class)
                  ->disableOriginalConstructor()
                  ->getMock();
        $metadata->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($class));
        $metadata->expects($this->any())
            ->method('getReflectionClass')
            ->will($this->returnValue(new \ReflectionClass($class)));

        $this->proxyGenerator->generateProxyClass($metadata, $this->proxyGenerator->getProxyFileName($class));

        require_once $this->proxyGenerator->getProxyFileName($class);

        return $proxyClass;
    }

    public function testResolveProxies()
    {
        $resolver = new EntityResolver();

        $proxyClass = $this->generateProxyClass(\stdClass::class);
        $proxy = new $proxyClass();

        $this->assertSame(\stdClass::class, $resolver->resolveNoExtend($proxyClass));
        $this->assertSame(\stdClass::class, $resolver->resolveNoExtend($proxy));
    }
}
