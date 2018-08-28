<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\EntityType;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Exception\InvalidFieldException;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $crudRegistry = $this->getMockBuilder(CrudRegistry::class)
                      ->disableOriginalConstructor()
                      ->getMock();

        return [
            'entity' => new EntityType($entityManager, $crudRegistry),
        ];
    }

    public function testListContext()
    {
        $entity = new \stdClass();
        $entity->user = new \stdClass();
        $entity->user->email = 'user@example.com';

        $this->config->add('user', [
            'type' => 'entity',
            'options' => [
                'display_field' => 'email',
                'class' => stdClass::class,
            ],
        ]);

        $this->assertTrimmedString('user@example.com', $this->listContext($entity, 'user'));
    }

    public function testListContextNoEntity()
    {
        $entity = new \stdClass();
        $entity->user = null;

        $this->config->add('user', [
            'type' => 'entity',
            'options' => [
                'class' => \stdClass::class,
                'display_field' => 'email',
            ],
        ]);

        $this->assertTrimmedString('', $this->listContext($entity, 'user'));
    }

    public function testNonEntityPropertyThrowsException()
    {
        $entity = new \stdClass();
        $entity->user = 100;

        $this->config->add('user', [
            'type' => 'entity',
            'options' => [
                'class' => \stdClass::class,
                'display_field' => 'email',
            ],
        ]);

        $this->setExpectedException(InvalidFieldException::class);
        $this->listContext($entity, 'user');
    }
}
