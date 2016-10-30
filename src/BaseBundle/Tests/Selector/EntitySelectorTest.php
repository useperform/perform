<?php

namespace Perform\BaseBundle\Tests\Selector;

use Perform\BaseBundle\Selector\EntitySelector;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Perform\BaseBundle\Type\TypeConfig;

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
    protected $qb;
    protected $typeConfig;

    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $this->qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->qb));

        $this->registry = $this->getMockBuilder('Perform\BaseBundle\Admin\AdminRegistry')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->typeConfig = $this->getMockBuilder('Perform\BaseBundle\Type\EntityTypeConfig')
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->selector = new EntitySelector($this->entityManager, $this->registry, $this->typeConfig);
    }

    protected function expectQueryBuilder($entityName)
    {
        $this->qb->expects($this->once())
            ->method('select')
            ->with('e')
            ->will($this->returnSelf());
        $this->qb->expects($this->once())
            ->method('from')
            ->with($entityName, 'e')
            ->will($this->returnSelf());
    }

    protected function expectTypeConfig($entityName, array $config)
    {
        $typeConfig = new TypeConfig();
        foreach ($config as $field => $config) {
            $typeConfig->add($field, $config);
        }

        $this->typeConfig->expects($this->once())
            ->method('getEntityTypeConfig')
            ->with($entityName)
            ->will($this->returnValue($typeConfig));
    }

    public function testDefaultListContext()
    {
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $result = $this->selector->listContext(new Request, 'Bundle:SomeEntity');

        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(Pagerfanta::class, $result[0]);
        $this->assertInternalType('array', $result[1]);
    }

    public function testListContextWithSorting()
    {
        $request = new Request;
        $request->query->set('sort', 'title');
        $request->query->set('direction', 'DESC');

        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', [
            'title' => [
                'type' => 'string',
                'sort' => true,
            ]
        ]);
        $this->qb->expects($this->once())
            ->method('orderBy')
            ->with('e.title', 'DESC');

        $this->selector->listContext($request, 'Bundle:SomeEntity');
    }

    public function testListContextWithDisabledSortField()
    {
        $request = new Request;
        $request->query->set('sort', 'enabled');
        $request->query->set('direction', 'DESC');

        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', [
            'enabled' => [
                'type' => 'boolean',
                'sort' => false,
            ]
        ]);
        $this->qb->expects($this->never())
            ->method('orderBy');

        $this->selector->listContext($request, 'Bundle:SomeEntity');
    }
}
