<?php

namespace Perform\BaseBundle\Tests\Action;

use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Action\DeleteAction;
use Perform\BaseBundle\Action\ActionResponse;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Crud\EntityManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DeleteActionTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $action;

    public function setUp()
    {
        $this->manager = $this->getMockBuilder(EntityManager::class)
                  ->disableOriginalConstructor()
                  ->getMock();
        $this->action = new DeleteAction($this->manager);
    }

    private function crudRequest()
    {
        return new CrudRequest('some_crud', CrudRequest::CONTEXT_ACTION);
    }

    public function testEntitiesAreDeleted()
    {
        $one = new \stdClass();
        $two = new \stdClass();
        $request = $this->crudRequest();
        $this->manager->expects($this->once())
            ->method('deleteMany')
            ->with($request, [$one, $two]);

        $this->action->run($request, [$one, $two], []);
    }

    public function testResponseRedirectsToCurrent()
    {
        $response = $this->action->run($this->crudRequest(), [], []);
        $this->assertInstanceOf(ActionResponse::class, $response);
        $this->assertSame(ActionResponse::REDIRECT_CURRENT, $response->getRedirect());
    }

    public function testResponseRedirectsToPreviousWhenViewingEntity()
    {
        $response = $this->action->run($this->crudRequest(), [], ['context' => CrudRequest::CONTEXT_VIEW]);
        $this->assertInstanceOf(ActionResponse::class, $response);
        $this->assertSame(ActionResponse::REDIRECT_PREVIOUS, $response->getRedirect());
    }
}
