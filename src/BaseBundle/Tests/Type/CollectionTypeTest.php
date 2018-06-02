<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\Type\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Perform\BaseBundle\Crud\CrudRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as CollectionFormType;

/**
 * CollectionTypeTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CollectionTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $type;

    public function setUp()
    {
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $assets = new AssetContainer();
        $this->type = new CollectionType($entityManager, $assets);
    }

    public function testListContextDefaultItemLabel()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $this->assertSame('0 items', $this->type->listContext($entity, 'items'));

        $entity->items = new ArrayCollection([1]);
        $this->assertSame('1 item', $this->type->listContext($entity, 'items'));

        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertSame('4 items', $this->type->listContext($entity, 'items'));
    }

    public function testListContextWithItemLabel()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $this->assertSame('0 things', $this->type->listContext($entity, 'items', ['itemLabel' => 'thing']));

        $entity->items = new ArrayCollection([1]);
        $this->assertSame('1 thing', $this->type->listContext($entity, 'items', ['itemLabel' => 'thing']));

        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertSame('4 things', $this->type->listContext($entity, 'items', ['itemLabel' => 'thing']));
    }

    public function testListContextWithItemLabelPlural()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $this->assertSame('0 things (a lot)', $this->type->listContext($entity, 'items', ['itemLabel' => ['thing', 'things (a lot)']]));

        $entity->items = new ArrayCollection([1]);
        $this->assertSame('1 thing', $this->type->listContext($entity, 'items', ['itemLabel' => ['thing', 'things (a lot)']]));

        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertSame('4 things (a lot)', $this->type->listContext($entity, 'items', ['itemLabel' => ['thing', 'things (a lot)']]));
    }

    public function testCreateContext()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $builder = $this->getMock(FormBuilderInterface::class);
        $builder->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($entity));

        $builder->expects($this->once())
            ->method('add')
            ->with('items', CollectionFormType::class);

        $this->type->createContext($builder, 'items', ['crud_name' => 'SomeRelation', 'sortField' => false]);
    }
}
