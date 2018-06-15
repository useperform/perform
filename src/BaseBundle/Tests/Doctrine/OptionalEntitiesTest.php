<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Test\TestKernel;
use Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\OptionalEntitiesBundle;
use Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\Entity\One;
use Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\Entity\Two;
use Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\Entity\ThreeOptional;
use Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\Entity\FourOptional;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class OptionalEntitiesTest extends \PHPUnit_Framework_TestCase
{
    protected $kernel;

    protected function kernel($configFile)
    {
        $this->kernel = new TestKernel([
            new OptionalEntitiesBundle(),
        ], [__DIR__.'/../Fixtures/OptionalEntitiesBundle/config/'.$configFile]);
        $this->kernel->boot();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    private function hasMapping($alias, $class)
    {
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $meta = $em->getClassMetadata($alias);
        $this->assertInstanceOf(ClassMetadata::class, $meta);
        $this->assertSame($class, $meta->name);
        $this->assertSame($meta, $em->getClassMetadata($class));
    }

    private function noMapping($class)
    {
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        try {
            $em->getClassMetadata($class);
            $this->fail(sprintf('ClassMetadata was found for %s when it was not supposed to be registered.', $class));
        } catch (MappingException $e) {
            $this->addToAssertionCount(1);
        }
    }

    public function testNoOptional()
    {
        $this->kernel('none.yml');

        $this->hasMapping('OptionalEntitiesBundle:One', One::class);
        $this->hasMapping('OptionalEntitiesBundle:Two', Two::class);
        $this->noMapping(ThreeOptional::class);
        $this->noMapping(FourOptional::class);
    }

    public function testOneOptional()
    {
        $this->kernel('three.yml');

        $this->hasMapping('OptionalEntitiesBundle:One', One::class);
        $this->hasMapping('OptionalEntitiesBundle:Two', Two::class);
        $this->hasMapping('OptionalEntitiesBundle:ThreeOptional', ThreeOptional::class);
        $this->noMapping(FourOptional::class);
    }

    public function testOtherOptional()
    {
        $this->kernel('four.yml');

        $this->hasMapping('OptionalEntitiesBundle:One', One::class);
        $this->hasMapping('OptionalEntitiesBundle:Two', Two::class);
        $this->noMapping(ThreeOptional::class);
        $this->hasMapping('OptionalEntitiesBundle:FourOptional', FourOptional::class);
    }

    public function testBothOptional()
    {
        $this->kernel('all.yml');

        $this->hasMapping('OptionalEntitiesBundle:One', One::class);
        $this->hasMapping('OptionalEntitiesBundle:Two', Two::class);
        $this->hasMapping('OptionalEntitiesBundle:ThreeOptional', ThreeOptional::class);
        $this->hasMapping('OptionalEntitiesBundle:FourOptional', FourOptional::class);
    }
}
