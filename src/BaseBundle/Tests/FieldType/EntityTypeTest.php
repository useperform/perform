<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\EntityType;
use Doctrine\ORM\EntityManagerInterface;
use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\Exception\InvalidTypeException;
use Perform\BaseBundle\Crud\CrudRegistry;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $type;

    public function setUp()
    {
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $crudRegistry = $this->getMockBuilder(CrudRegistry::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->type = new EntityType($entityManager, $crudRegistry);
    }

    public function testListContext()
    {
        $user = new User();
        $user->setEmail('user@example.com');

        $entity = new \stdClass();
        $entity->user = $user;

        $options = [
            'class' => 'PerformUserBundle:User',
            'crud_name' => 'some_crud',
            'display_field' => 'email',
            'link_to' => false,
            'multiple' => false,
        ];

        $expected = [
            'crud_name' => 'some_crud',
            'value' => $user,
            'display_field' => 'email',
            'link_to' => false,
            'multiple' => false,
        ];
        $this->assertSame($expected, $this->type->listContext($entity, 'user', $options));
    }

    public function testListContextWithNoEntity()
    {
        $entity = new \stdClass();
        $entity->user = null;

        $options = [
            'class' => 'PerformUserBundle:User',
            'display_field' => 'email',
            'multiple' => false,
        ];

        $this->assertSame('', $this->type->listContext($entity, 'user', $options));
    }

    public function testNonEntityPropertyThrowsException()
    {
        $entity = new \stdClass();
        $entity->user = 100;

        $options = [
            'class' => 'PerformUserBundle:User',
            'display_field' => 'email',
        ];

        $this->setExpectedException(InvalidTypeException::class);
        $this->type->listContext($entity, 'user', $options);
    }
}
