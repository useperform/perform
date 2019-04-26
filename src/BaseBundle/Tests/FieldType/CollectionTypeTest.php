<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as CollectionFormType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class CollectionTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    public function registerTypes()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $assets = new AssetContainer();

        return [
            'collection' => new CollectionType($entityManager, $assets),
        ];
    }

    public function testListContextDefaultItemLabel()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $this->config->add('items', [
            'type' => 'collection',
            'options' => [
                'crud_name' => 'some_crud',
            ]
        ]);

        $this->assertTrimmedString('0 items', $this->listContext($entity, 'items'));
        $entity->items = new ArrayCollection([1]);
        $this->assertTrimmedString('1 item', $this->listContext($entity, 'items'));
        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertTrimmedString('4 items', $this->listContext($entity, 'items'));
    }

    public function testListContextWithItemLabel()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $this->config->add('items', [
            'type' => 'collection',
            'options' => [
                'crud_name' => 'some_crud',
                'item_label' => 'test_key',
            ]
        ]);

        $this->assertTrimmedString('0 test_key', $this->listContext($entity, 'items'));
        $entity->items = new ArrayCollection([1]);
        $this->assertTrimmedString('1 test_key', $this->listContext($entity, 'items'));
        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertTrimmedString('4 test_key', $this->listContext($entity, 'items'));
    }

    public function testListContextWithNoItemLabel()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $this->config->add('items', [
            'type' => 'collection',
            'options' => [
                'crud_name' => 'some_crud',
                'item_label' => false,
            ]
        ]);

        $this->assertTrimmedString('0', $this->listContext($entity, 'items'));
        $entity->items = new ArrayCollection([1]);
        $this->assertTrimmedString('1', $this->listContext($entity, 'items'));
        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertTrimmedString('4', $this->listContext($entity, 'items'));
    }

    public function testCreateContext()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection();
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($entity));

        $builder->expects($this->once())
            ->method('add')
            ->with('items', CollectionFormType::class);

        $this->getType('collection')->createContext($builder, 'items', [
            'crud_name' => 'SomeRelation',
            'sort_field' => false,
            'form_options' => [],
        ]);
    }
}
