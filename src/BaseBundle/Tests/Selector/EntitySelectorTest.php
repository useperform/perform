<?php

namespace Perform\BaseBundle\Tests\Selector;

use Perform\BaseBundle\Selector\EntitySelector;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;

/**
 * EntitySelectorTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelectorTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $registry;
    protected $selector;

    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['from'])
            ->getMock();

        $this->entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));

        $this->registry = $this->getMockBuilder('Perform\BaseBundle\Admin\AdminRegistry')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->selector = new EntitySelector($this->entityManager, $this->registry);
    }

    public function testDefaultListContext()
    {
        $request = new Request;
        $result = $this->selector->listContext($request, 'PerformBaseBundle:User');
        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(Pagerfanta::class, $result[0]);
        $this->assertInternalType('array', $result[1]);
    }
}
