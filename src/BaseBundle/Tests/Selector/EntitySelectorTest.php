<?php

namespace Perform\BaseBundle\Tests\Selector;

use Perform\BaseBundle\Selector\EntitySelector;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Admin\AdminRequest;
use Perform\BaseBundle\Type\BooleanType;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelectorTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $qb;
    protected $typeRegistry;
    protected $store;
    protected $selector;

    public function setUp()
    {
        $this->entityManager = $this->getMock(EntityManagerInterface::class);
        $this->qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->qb));

        $this->typeRegistry = Services::typeRegistry([
            'string' => new StringType(),
            'boolean' => new BooleanType(),
        ]);
        $this->store = $this->getMock(ConfigStoreInterface::class);
        $this->store->expects($this->any())
            ->method('getFilterConfig')
            ->will($this->returnValue(new FilterConfig([])));

        $this->selector = new EntitySelector($this->entityManager, $this->store);
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
        $typeConfig = new TypeConfig($this->typeRegistry);
        foreach ($config as $field => $config) {
            $typeConfig->add($field, $config);
        }

        $this->store->expects($this->any())
            ->method('getTypeConfig')
            ->with($entityName)
            ->will($this->returnValue($typeConfig));
    }

    public function testDefaultListContext()
    {
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', []);
        $result = $this->selector->listContext(new AdminRequest(new Request(), TypeConfig::CONTEXT_LIST), 'Bundle:SomeEntity');

        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(Pagerfanta::class, $result[0]);
        $this->assertInternalType('array', $result[1]);
    }

    public function testListContextWithSorting()
    {
        $request = new Request();
        $request->query->set('sort', 'title');
        $request->query->set('direction', 'DESC');

        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', [
            'title' => [
                'type' => 'string',
                'sort' => true,
            ],
        ]);
        $this->qb->expects($this->once())
            ->method('orderBy')
            ->with('e.title', 'DESC')
            ->will($this->returnSelf());

        $this->selector->listContext(new AdminRequest($request, TypeConfig::CONTEXT_LIST), 'Bundle:SomeEntity');
    }

    public function testListContextWithDisabledSortField()
    {
        $request = new Request();
        $request->query->set('sort', 'enabled');
        $request->query->set('direction', 'DESC');

        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', [
            'enabled' => [
                'type' => 'boolean',
                'sort' => false,
            ],
        ]);
        $this->qb->expects($this->never())
            ->method('orderBy');

        $this->selector->listContext(new AdminRequest($request, TypeConfig::CONTEXT_LIST), 'Bundle:SomeEntity');
    }

    public function testListContextWithCustomSorting()
    {
        $request = new Request();
        $request->query->set('sort', 'fullname');
        $request->query->set('direction', 'DESC');

        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', [
            'fullname' => [
                'type' => 'string',
                'sort' => function ($qb, $direction) {
                    return $qb->orderBy('e.forename', $direction)
                        ->addOrderBy('e.surname', $direction);
                },
            ],
        ]);
        $this->qb->expects($this->once())
            ->method('orderBy')
            ->with('e.forename', 'DESC')
            ->will($this->returnSelf());
        $this->qb->expects($this->once())
            ->method('addOrderBy')
            ->with('e.surname', 'DESC')
            ->will($this->returnSelf());

        $this->selector->listContext(new AdminRequest($request, TypeConfig::CONTEXT_LIST), 'Bundle:SomeEntity');
    }

    public function testListContextWithCustomSortQueryBuilder()
    {
        $request = new Request();
        $request->query->set('sort', 'fullname');
        $request->query->set('direction', 'DESC');

        $differentQb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
                     ->disableOriginalConstructor()
                     ->getMock();
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', [
            'fullname' => [
                'type' => 'string',
                'sort' => function ($qb, $direction) use ($differentQb) {
                    return $differentQb;
                },
            ],
        ]);

        $differentQb->expects($this->once())
            ->method('getQuery');
        $this->selector->listContext(new AdminRequest($request, TypeConfig::CONTEXT_LIST), 'Bundle:SomeEntity');
    }

    public function testInvalidSortFunctionThrowsException()
    {
        $request = new Request();
        $request->query->set('sort', 'fullname');
        $request->query->set('direction', 'DESC');

        $differentQb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
                     ->disableOriginalConstructor()
                     ->getMock();
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $this->expectTypeConfig('Bundle:SomeEntity', [
            'fullname' => [
                'type' => 'string',
                'sort' => function ($qb, $direction) use ($differentQb) {
                },
            ],
        ]);

        $this->setExpectedException('\UnexpectedValueException');
        $this->selector->listContext(new AdminRequest($request, TypeConfig::CONTEXT_LIST), 'Bundle:SomeEntity');
    }
}
