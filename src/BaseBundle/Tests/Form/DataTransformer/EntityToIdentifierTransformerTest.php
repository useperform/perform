<?php

namespace Perform\BaseBundle\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Form\DataTransformer\EntityToIdentifierTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityToIdentifierTransformerTest extends TestCase
{
    protected $em;
    protected $uow;
    protected $repo;
    protected $transformer;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->uow = $this->getMockBuilder(UnitOfWork::class)
                   ->disableOriginalConstructor()
                   ->getMock();
        $this->repo = $this->getMockBuilder(EntityRepository::class)
                   ->disableOriginalConstructor()
                   ->getMock();
        $this->em->expects($this->any())
            ->method('getUnitOfWork')
            ->will($this->returnValue($this->uow));
        $this->em->expects($this->any())
            ->method('getRepository')
            ->with('SomeBundle:Item')
            ->will($this->returnValue($this->repo));

        $this->transformer = new EntityToIdentifierTransformer($this->em, 'SomeBundle:Item');
    }

    public function testTransform()
    {
        $obj = new \stdClass();
        $this->uow->expects($this->any())
            ->method('getSingleIdentifierValue')
            ->with($obj)
            ->will($this->returnValue(1));

        $this->assertSame(1, $this->transformer->transform($obj));
    }

    public function testTransformNull()
    {
        $this->assertNull($this->transformer->transform(null));
    }

    public function testReverseTransform()
    {
        $obj = new \stdClass();
        $this->repo->expects($this->once())
            ->method('find')
            ->with(1)
            ->will($this->returnValue($obj));

        $this->assertSame($obj, $this->transformer->reverseTransform(1));
    }

    public function testReverseTransformNull()
    {
        $this->assertNull($this->transformer->reverseTransform(null));
    }
}
