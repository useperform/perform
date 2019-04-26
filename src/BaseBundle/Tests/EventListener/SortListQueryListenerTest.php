<?php

namespace Perform\BaseBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\EventListener\SortListQueryListener;
use Perform\BaseBundle\Config\FieldConfig;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Test\Services;
use Perform\BaseBundle\FieldType\StringType;
use Perform\BaseBundle\FieldType\BooleanType;
use Perform\BaseBundle\Event\QueryEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SortListQueryListenerTest extends TestCase
{
    protected $store;
    protected $qb;
    protected $listener;

    public function setUp()
    {
        $this->qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->store = $this->createMock(ConfigStoreInterface::class);
        $this->typeRegistry = Services::fieldTypeRegistry([
            'string' => new StringType(),
            'boolean' => new BooleanType(),
        ]);

        $this->listener = new SortListQueryListener($this->store);
    }

    protected function expectFieldConfig($entityName, array $config)
    {
        $typeConfig = new FieldConfig($this->typeRegistry);
        foreach ($config as $field => $config) {
            $typeConfig->add($field, $config);
        }

        $this->store->expects($this->any())
            ->method('getFieldConfig')
            ->with($entityName)
            ->will($this->returnValue($typeConfig));
    }

    public function testNoSort()
    {
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);

        $this->expectFieldConfig('some_crud', [
            'enabled' => [
                'type' => 'boolean',
                'sort' => true,
            ],
        ]);
        $this->qb->expects($this->never())
            ->method('orderBy');

        $this->listener->onListQuery(new QueryEvent($this->qb, $request));
    }

    public function testSort()
    {
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $request->setSortField('title');
        $request->setSortDirection('DESC');

        $this->expectFieldConfig('some_crud', [
            'title' => [
                'type' => 'string',
                'sort' => true,
            ],
        ]);
        $this->qb->expects($this->once())
            ->method('orderBy')
            ->with('e.title', 'DESC')
            ->will($this->returnSelf());

        $this->listener->onListQuery(new QueryEvent($this->qb, $request));
    }

    public function testSortWithDisabledField()
    {
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $request->setSortField('enabled');
        $request->setSortDirection('DESC');

        $this->expectFieldConfig('some_crud', [
            'enabled' => [
                'type' => 'boolean',
                'sort' => false,
            ],
        ]);
        $this->qb->expects($this->never())
            ->method('orderBy');

        $this->listener->onListQuery(new QueryEvent($this->qb, $request));
    }

    public function testSortWithCustomFunction()
    {
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $request->setSortField('fullname');
        $request->setSortDirection('DESC');

        $this->expectFieldConfig('some_crud', [
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

        $this->listener->onListQuery(new QueryEvent($this->qb, $request));
    }

    public function testSortWithCustomQueryBuilder()
    {
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $request->setSortField('fullname');
        $request->setSortDirection('DESC');

        $differentQb = $this->getMockBuilder(QueryBuilder::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        $this->expectFieldConfig('some_crud', [
            'fullname' => [
                'type' => 'string',
                'sort' => function ($qb, $direction) use ($differentQb) {
                    return $differentQb;
                },
            ],
        ]);
        $event = new QueryEvent($this->qb, $request);
        $this->listener->onListQuery($event);
        $this->assertSame($differentQb, $event->getQueryBuilder());
    }

    public function testSortWithInvalidCustomFunction()
    {
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $request->setSortField('fullname');
        $request->setSortDirection('DESC');

        $this->expectFieldConfig('some_crud', [
            'fullname' => [
                'type' => 'string',
                'sort' => function ($qb, $direction) {
                    return new \stdClass();
                },
            ],
        ]);

        $this->expectException(\UnexpectedValueException::class);
        $this->listener->onListQuery(new QueryEvent($this->qb, $request));
    }
}
