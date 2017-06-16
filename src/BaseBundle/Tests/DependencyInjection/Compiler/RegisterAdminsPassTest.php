<?php

namespace BaseBundle\Tests\DependencyInjection\Compiler;

use Perform\BaseBundle\DependencyInjection\Compiler\RegisterAdminsPass;
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
        $this->registry = $this->container->register('perform_base.admin.registry', 'Perform\BaseBundle\Type\TypeRegistry');
        $this->container->setParameter('perform_base.admins', []);
        $this->container->setParameter('perform_base.extended_entities', []);
    }

    public function testIsCompilerPass()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->pass);
    }

    public function testRegisterAdmins()
    {
        $this->container->setParameter('perform_base.entity_aliases', [
            'TestBundle:Foo' => 'TestBundle\Entity\Foo',
            'TestBundle:Bar' => 'TestBundle\Entity\Bar',
        ]);
        $this->container->register('test.admin.foo', 'TestBundle\Admin\FooAdmin')
            ->addTag('perform_base.admin', ['entity' => 'TestBundle:Foo']);
        $this->container->register('test.admin.bar', 'TestBundle\Admin\BarAdmin')
            ->addTag('perform_base.admin', ['entity' => 'TestBundle:Bar']);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                ['TestBundle\Entity\Foo', 'test.admin.foo'],
            ],
            [
                'addAdmin',
                ['TestBundle\Entity\Bar', 'test.admin.bar'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testExtendedEntitiesAreSkipped()
    {
        //an entity has been extended, but the same admin is being used (no
        //admin registered for the extended entity).
        $this->container->setParameter('perform_base.entity_aliases', [
            'PerformBaseBundle:Foo' => 'Perform\BaseBundle\Entity\Foo',
            'TestBundle:Foo' => 'TestBundle\Entity\Foo',
        ]);
        $this->container->register('perform_base.admin.foo', 'Perform\BaseBundle\Admin\FooAdmin')
            ->addTag('perform_base.admin', ['entity' => 'PerformBaseBundle:Foo']);
        $this->container->setParameter('perform_base.extended_entities', [
             'Perform\BaseBundle\Entity\Foo' => 'TestBundle\Entity\Foo',
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                ['TestBundle\Entity\Foo', 'perform_base.admin.foo'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testChildEntitiesUseNewAdmin()
    {
        //an entity has been extended, and a new admin is being used.
        $this->container->setParameter('perform_base.entity_aliases', [
            'PerformBaseBundle:Foo' => 'Perform\BaseBundle\Entity\Foo',
            'TestBundle:Foo' => 'TestBundle\Entity\Foo',
        ]);
        $this->container->register('perform_base.admin.foo', 'Perform\BaseBundle\Admin\FooAdmin')
            ->addTag('perform_base.admin', ['entity' => 'PerformBaseBundle:Foo']);
        $this->container->register('test.admin.foo', 'TestBundle\Admin\FooAdmin')
            ->addTag('perform_base.admin', ['entity' => 'TestBundle:Foo']);
        $this->container->setParameter('perform_base.extended_entities', [
             'Perform\BaseBundle\Entity\Foo' => 'TestBundle\Entity\Foo',
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                ['TestBundle\Entity\Foo', 'test.admin.foo'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }
}
