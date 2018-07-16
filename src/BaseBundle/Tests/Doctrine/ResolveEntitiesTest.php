<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Test\TestKernel;
use Perform\BaseBundle\Tests\Fixtures\ResolveEntities\ResolveBundle\ResolveBundle;
use Perform\BaseBundle\Tests\Fixtures\ResolveEntities\ResolveBundle\Entity\Dog;
use Perform\BaseBundle\Tests\Fixtures\ResolveEntities\ResolveBundle\Entity\Cat;
use Doctrine\ORM\Mapping\MappingException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class ResolveEntitiesTest extends \PHPUnit_Framework_TestCase
{
    protected $kernel;

    protected function setUp()
    {
        $this->kernel = new TestKernel([
            new ResolveBundle(),
        ], [__DIR__.'/../Fixtures/ResolveEntities/config.yml']);
        $this->kernel->boot();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    public function testResolveEntities()
    {
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $man = $em->getClassMetadata('ResolveBundle:Man');
        $dog = $man->getAssociationMapping('pet');
        $this->assertSame(Dog::class, $dog['targetEntity']);

        $woman = $em->getClassMetadata('ResolveBundle:Woman');
        $cat = $woman->getAssociationMapping('pet');
        $this->assertSame(Cat::class, $cat['targetEntity']);
    }

    public function testResolveToMappedSuperclassThrowException()
    {
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->setExpectedException(MappingException::class);
        $em->getClassMetadata('ResolveBundle:BuildingInterface');
    }
}
