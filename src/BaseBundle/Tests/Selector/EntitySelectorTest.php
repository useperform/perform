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

        $this->typeRegistry = Services::typeRegistry([
            'string' => new StringType(),
            'boolean' => new BooleanType(),
        ]);
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->store = $this->getMock(ConfigStoreInterface::class);
        $this->store->expects($this->any())
            ->method('getFilterConfig')
            ->will($this->returnValue(new FilterConfig([])));

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
        $result = $this->selector->listContext($request);

        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(Pagerfanta::class, $result[0]);
        $this->assertInternalType('array', $result[1]);
    }

    public function testMissingEntityClassThrowsException()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->selector->listContext(new CrudRequest(CrudRequest::CONTEXT_LIST));
    }
}
