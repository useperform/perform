<?php

namespace Perform\BaseBundle\Tests\Selector;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Selector\EntitySelector;
use Pagerfanta\Pagerfanta;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\FieldType\StringType;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\FieldType\BooleanType;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Test\Services;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\BaseBundle\Event\QueryEvent;
use Perform\BaseBundle\Config\FieldConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelectorTest extends TestCase
{
    protected $em;
    protected $qb;
    protected $typeRegistry;
    protected $store;
    protected $selector;

    public function setUp()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $this->qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->qb));
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->store = $this->createMock(ConfigStoreInterface::class);
        $this->typeConfig = $this->getMockBuilder(FieldConfig::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->store->expects($this->any())
            ->method('getFieldConfig')
            ->will($this->returnValue($this->typeConfig));
        $this->filterConfig = $this->getMockBuilder(FilterConfig::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->store->expects($this->any())
            ->method('getFilterConfig')
            ->will($this->returnValue($this->filterConfig));

        $this->selector = new EntitySelector($em, $this->dispatcher, $this->store);
    }

    protected function expectQueryBuilder($entityClass)
    {
        $this->store->expects($this->any())
            ->method('getEntityClass')
            ->with('some_crud')
            ->will($this->returnValue($entityClass));
        $this->qb->expects($this->any())
            ->method('select')
            ->with('e')
            ->will($this->returnSelf());
        $this->qb->expects($this->any())
            ->method('from')
            ->with($entityClass, 'e')
            ->will($this->returnSelf());
    }

    public function testGetQueryBuilder()
    {
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(QueryEvent::LIST_QUERY, $this->callback(function ($e) use ($request) {
                return $e instanceof QueryEvent
                        && $e->getQueryBuilder() === $this->qb
                        && $e->getCrudRequest() === $request;
            }));

        $qb = $this->selector->getQueryBuilder($request);
        $this->assertSame($this->qb, $qb);
    }

    public function testListContext()
    {
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $request = new CrudRequest('some_crud', CrudRequest::CONTEXT_LIST);
        $this->filterConfig->expects($this->any())
            ->method('getDefault')
            ->will($this->returnValue('some_filter'));
        $this->typeConfig->expects($this->any())
            ->method('getDefaultSort')
            ->will($this->returnValue(['sort_field', 'DESC']));

        $result = $this->selector->listContext($request);

        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(Pagerfanta::class, $result[0]);
        $this->assertInternalType('array', $result[1]);

        // assert that the defaults have been applied to the request
        $this->assertSame('some_filter', $request->getFilter());
        $this->assertSame('sort_field', $request->getSortField());
        $this->assertSame('DESC', $request->getSortDirection());
    }
}
