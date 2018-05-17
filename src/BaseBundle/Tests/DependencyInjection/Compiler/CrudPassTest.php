<?php

namespace BaseBundle\Tests\DependencyInjection\Compiler;

use Perform\BaseBundle\DependencyInjection\Compiler\CrudPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlParentBundle\Entity\Item;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlParentBundle\Entity\ItemLink;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\Entity\XmlItem;
use Perform\BaseBundle\Crud\InvalidCrudException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;
    protected $container;

    public function setUp()
    {
        $this->pass = new CrudPass();
        $this->container = new ContainerBuilder();
        $this->registry = $this->container->register('perform_base.crud.registry', 'Perform\BaseBundle\Type\TypeRegistry');
        $this->container->setParameter('perform_base.admins', []);
        $this->container->setParameter('perform_base.extended_entities', []);
    }

    public function testIsCompilerPass()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->pass);
    }

    public function testRegisterCrud()
    {
        $this->container->setParameter('perform_base.entity_aliases', [
            'ParentBundle:Item' => Item::class,
            'ParentBundle:ItemLink' => ItemLink::class,
        ]);
        $this->container->register('parent.crud.item', 'ParentBundle\Crud\ItemCrud')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);
        $this->container->register('parent.crud.item_link', 'ParentBundle\Crud\ItemLinkCrud')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:ItemLink']);

        $this->pass->process($this->container);
        $calls = [
            [
                'addCrud',
                [Item::class, 'parent.crud.item'],
            ],
            [
                'addCrud',
                [ItemLink::class, 'parent.crud.item_link'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testRegisterCrudWithClassname()
    {
        $this->container->setParameter('perform_base.entity_aliases', [
            'ParentBundle:Item' => Item::class,
        ]);
        $this->container->register('parent.crud.item', 'ParentBundle\Crud\ItemCrud')
            ->addTag('perform_base.admin', ['entity' => Item::class]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addCrud',
                [Item::class, 'parent.crud.item'],
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
        $this->container->register('parent.crud.item', 'ParentBundle\Crud\ItemCrud')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);
        $this->container->setParameter('perform_base.extended_entities', [
            Item::class => XmlItem::class,
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addCrud',
                [XmlItem::class, 'parent.crud.item'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testChildEntitiesUseNewCrud()
    {
        //an entity has been extended, and a new admin is being used.
        $this->container->setParameter('perform_base.entity_aliases', [
            'ParentBundle:Item' => Item::class,
            'ChildBundle:XmlItem' => XmlItem::class,
        ]);
        $this->container->register('parent.crud.item', 'ParentBundle\Crud\ItemCrud')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);
        $this->container->register('child.crud.xml_item', 'ChildBundle\Crud\XmlItemCrud')
            ->addTag('perform_base.admin', ['entity' => 'ChildBundle:XmlItem']);
        $this->container->setParameter('perform_base.extended_entities', [
            Item::class => XmlItem::class,
        ]);

        $this->pass->process($this->container);
        $calls = [
            [
                'addCrud',
                [XmlItem::class, 'child.crud.xml_item'],
            ],
        ];
        $this->assertSame($calls, $this->registry->getMethodCalls());
    }

    public function testUnknownClassThrowsException()
    {
        $this->container->setParameter('perform_base.entity_aliases', []);
        $this->container->register('parent.crud.item', 'ParentBundle\Crud\ItemCrud')
            ->addTag('perform_base.admin', ['entity' => 'ParentBundle:Item']);

        $this->setExpectedException(InvalidCrudException::class);
        $this->pass->process($this->container);
    }
}
