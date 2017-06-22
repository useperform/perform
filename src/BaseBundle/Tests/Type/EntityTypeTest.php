<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\EntityType;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Entity\User;
use Perform\BaseBundle\Exception\InvalidTypeException;

/**
 * EntityTypeTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $type;

    public function setUp()
    {
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $this->type = new EntityType($entityManager);
    }

    public function testListContext()
    {
        $user = new User();
        $user->setEmail('user@example.com');

        $entity = new \stdClass();
        $entity->user = $user;

        $options = [
            'class' => 'PerformBaseBundle:User',
            'display_field' => 'email',
            'link_to' => false,
        ];

        $expected = [
            'value' => 'user@example.com',
            'related_entity' => $user,
            'link_to' => false,
        ];
        $this->assertSame($expected, $this->type->listContext($entity, 'user', $options));
    }

    public function testListContextWithNoEntity()
    {
        $entity = new \stdClass();
        $entity->user = null;

        $options = [
            'class' => 'PerformBaseBundle:User',
            'display_field' => 'email',
        ];

        $this->assertSame('', $this->type->listContext($entity, 'user', $options));
    }

    public function testNonEntityPropertyThrowsException()
    {
        $entity = new \stdClass();
        $entity->user = 100;

        $options = [
            'class' => 'PerformBaseBundle:User',
            'display_field' => 'email',
        ];

        $this->setExpectedException(InvalidTypeException::class);
        $this->type->listContext($entity, 'user', $options);
    }
}
