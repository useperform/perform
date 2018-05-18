<?php

namespace Perform\BaseBundle\Tests\Selector;

use Perform\BaseBundle\Selector\EntitySelector;
use Pagerfanta\Pagerfanta;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Type\BooleanType;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Test\Services;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\BaseBundle\Event\ListQueryEvent;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelectorTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $qb;
    protected $typeRegistry;
    protected $store;
    protected $selector;

    public function setUp()
    {
        $em = $this->getMock(EntityManagerInterface::class);
        $this->qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->qb));
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->store = $this->getMock(ConfigStoreInterface::class);
        $this->typeConfig = $this->getMockBuilder(TypeConfig::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->store->expects($this->any())
            ->method('getTypeConfig')
            ->will($this->returnValue($this->typeConfig));
        $this->filterConfig = $this->getMockBuilder(FilterConfig::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->store->expects($this->any())
            ->method('getFilterConfig')
            ->will($this->returnValue($this->filterConfig));

        $this->selector = new EntitySelector($em, $this->dispatcher, $this->store);
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

    public function testGetQueryBuilder()
    {
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $request = new CrudRequest(CrudRequest::CONTEXT_LIST);
        $request->setEntityClass('Bundle:SomeEntity');

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(ListQueryEvent::NAME, $this->callback(function ($e) use ($request) {
                return $e instanceof ListQueryEvent
                        && $e->getQueryBuilder() === $this->qb
                        && $e->getCrudRequest() === $request;
            }));

        $qb = $this->selector->getQueryBuilder($request);
        $this->assertSame($this->qb, $qb);
    }

    public function testListContext()
    {
        $this->expectQueryBuilder('Bundle:SomeEntity');
        $request = new CrudRequest(CrudRequest::CONTEXT_LIST);
        $request->setEntityClass('Bundle:SomeEntity');
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

    public function testMissingEntityClassThrowsException()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->selector->listContext(new CrudRequest(CrudRequest::CONTEXT_LIST));
    }
}
