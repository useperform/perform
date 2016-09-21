<?php

namespace Base\Tests\DependencyInjection\Compiler;

use Perform\Base\DependencyInjection\Compiler\RegisterAdminsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * RegisterAdminsPassTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterAdminsPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;
    protected $container;

    public function setUp()
    {
        $this->pass = new RegisterAdminsPass();
        $this->container = new ContainerBuilder();
        $this->registry = $this->container->register('admin_base.admin.registry', 'Perform\Base\Type\TypeRegistry');
        $this->container->setParameter('admin_base.admins', []);
        $this->container->setParameter('admin_base.extended_entity_aliases', []);
    }

    public function testIsCompilerPass()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->pass);
    }

    public function testRegisterAdmins()
    {
        $this->container->setParameter('admin_base.entity_aliases', [
            'TestBundle:Foo' => 'TestBundle\Entity\Foo',
            'TestBundle:Bar' => 'TestBundle\Entity\Bar',
        ]);
        $this->container->register('test.admin.foo', 'TestBundle\Admin\FooAdmin')
            ->addTag('admin_base.admin', ['entity' => 'TestBundle:Foo']);
        $this->container->register('test.admin.bar', 'TestBundle\Admin\BarAdmin')
            ->addTag('admin_base.admin', ['entity' => 'TestBundle:Bar']);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                ['TestBundle:Foo', 'TestBundle\Entity\Foo', 'test.admin.foo'],
            ],
            [
                'addAdmin',
                ['TestBundle:Bar', 'TestBundle\Entity\Bar', 'test.admin.bar'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testExtendedEntitiesAreRegistered()
    {
        //an entity has been extended, but the same admin is being used (no
        //admin registered for the extended entity).
        $this->container->setParameter('admin_base.entity_aliases', [
            'PerformBaseBundle:Foo' => 'Perform\BaseBundle\Entity\Foo',
            'TestBundle:Foo' => 'TestBundle\Entity\Foo',
        ]);
        $this->container->register('admin_base.admin.foo', 'Perform\BaseBundle\Admin\FooAdmin')
            ->addTag('admin_base.admin', ['entity' => 'PerformBaseBundle:Foo']);
        $this->container->setParameter('admin_base.extended_entity_aliases', [
            'PerformBaseBundle:Foo' => 'TestBundle:Foo',
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                ['PerformBaseBundle:Foo', 'Perform\BaseBundle\Entity\Foo', 'admin_base.admin.foo'],
            ],
            [
                'addAdmin',
                ['TestBundle:Foo', 'TestBundle\Entity\Foo', 'admin_base.admin.foo'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testExtendedEntitiesUseNewAdmin()
    {
        //an entity has been extended, and a new admin is being used.
        $this->container->setParameter('admin_base.entity_aliases', [
            'PerformBaseBundle:Foo' => 'Perform\BaseBundle\Entity\Foo',
            'TestBundle:Foo' => 'TestBundle\Entity\Foo',
        ]);
        $this->container->register('admin_base.admin.foo', 'Perform\BaseBundle\Admin\FooAdmin')
            ->addTag('admin_base.admin', ['entity' => 'PerformBaseBundle:Foo']);
        $this->container->register('test.admin.foo', 'TestBundle\Admin\FooAdmin')
            ->addTag('admin_base.admin', ['entity' => 'TestBundle:Foo']);
        $this->container->setParameter('admin_base.extended_entity_aliases', [
            'PerformBaseBundle:Foo' => 'TestBundle:Foo',
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                ['PerformBaseBundle:Foo', 'Perform\BaseBundle\Entity\Foo', 'test.admin.foo'],
            ],
            [
                'addAdmin',
                ['TestBundle:Foo', 'TestBundle\Entity\Foo', 'test.admin.foo'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }
}
