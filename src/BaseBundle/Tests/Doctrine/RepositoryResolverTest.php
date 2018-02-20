<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Doctrine\RepositoryResolver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RepositoryResolverTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->entityResolver = $this->getMock(EntityResolver::class);
        $this->resolver = new RepositoryResolver($this->em, $this->entityResolver);
    }

    public function testGetRepository()
    {
        $repo = $this->getMockBuilder(EntityRepository::class)
              ->disableOriginalConstructor()
              ->getMock();
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with('Bundle\Entity\ExtendingClass')
            ->will($this->returnValue($repo));
        $this->entityResolver->expects($this->once())
            ->method('resolve')
            ->with('Bundle\Entity\ParentClass')
            ->will($this->returnValue('Bundle\Entity\ExtendingClass'));

        $this->assertSame($repo, $this->resolver->getRepository('Bundle\Entity\ParentClass'));
    }
}
