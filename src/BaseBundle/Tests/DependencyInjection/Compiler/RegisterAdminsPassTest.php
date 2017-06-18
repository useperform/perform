<?php

namespace BaseBundle\Tests\DependencyInjection\Compiler;

use Perform\BaseBundle\DependencyInjection\Compiler\RegisterAdminsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlParentBundle\Entity\Item;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlParentBundle\Entity\ItemLink;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\Entity\XmlItem;
use Perform\BaseBundle\Exception\InvalidAdminException;

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
            'ParentBundle:Item' => Item::class,
            'ParentBundle:ItemLink' => ItemLink::class,
        ]);
        $this->container->register('parent.admin.item', 'ParentBundle\Admin\ItemAdmin')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);
        $this->container->register('parent.admin.item_link', 'ParentBundle\Admin\ItemLinkAdmin')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:ItemLink']);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                [Item::class, 'parent.admin.item'],
            ],
            [
                'addAdmin',
                [ItemLink::class, 'parent.admin.item_link'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testRegisterAdminWithClassname()
    {
        $this->container->setParameter('perform_base.entity_aliases', [
            'ParentBundle:Item' => Item::class,
        ]);
        $this->container->register('parent.admin.item', 'ParentBundle\Admin\ItemAdmin')
            ->addTag('perform_base.admin', ['entity' => Item::class]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                [Item::class, 'parent.admin.item'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testExtendedEntitiesAreSkipped()
    {
        //an entity has been extended, but the same admin is being used (no
        //admin registered for the extended entity).
        $this->container->setParameter('perform_base.entity_aliases', [
            'ParentBundle:Item' => Item::class,
            'ChildBundle:XmlItem' => XmlItem::class,
        ]);
        $this->container->register('parent.admin.item', 'ParentBundle\Admin\ItemAdmin')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);
        $this->container->setParameter('perform_base.extended_entities', [
            Item::class => XmlItem::class,
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                [XmlItem::class, 'parent.admin.item'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testChildEntitiesUseNewAdmin()
    {
        //an entity has been extended, and a new admin is being used.
        $this->container->setParameter('perform_base.entity_aliases', [
            'ParentBundle:Item' => Item::class,
            'ChildBundle:XmlItem' => XmlItem::class,
        ]);
        $this->container->register('parent.admin.item', 'ParentBundle\Admin\ItemAdmin')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);
        $this->container->register('child.admin.xml_item', 'ChildBundle\Admin\XmlItemAdmin')
            ->addTag('perform_base.admin', ['entity' => 'ChildBundle:XmlItem']);
        $this->container->setParameter('perform_base.extended_entities', [
            Item::class => XmlItem::class,
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addAdmin',
                [XmlItem::class, 'child.admin.xml_item'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testUnknownClassThrowsException()
    {
        $this->container->setParameter('perform_base.entity_aliases', []);
        $this->container->register('parent.admin.item', 'ParentBundle\Admin\ItemAdmin')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);

        $this->setExpectedException(InvalidAdminException::class);
        $this->pass->process($this->container);
    }
}
