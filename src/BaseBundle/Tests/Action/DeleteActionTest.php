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

    public function testEntitiesAreDeleted()
    {
        $one = new \stdClass();
        $two = new \stdClass();
        $this->manager->expects($this->once())
            ->method('deleteMany')
            ->with([$one, $two]);

        $this->action->run([$one, $two], []);
    }

    public function testResponseRedirectsToCurrent()
    {
        $response = $this->action->run([], []);
        $this->assertInstanceOf(ActionResponse::class, $response);
        $this->assertSame(ActionResponse::REDIRECT_CURRENT, $response->getRedirect());
    }

    public function testResponseRedirectsToPreviousWhenViewingEntity()
    {
        $response = $this->action->run([], ['context' => CrudRequest::CONTEXT_VIEW]);
        $this->assertInstanceOf(ActionResponse::class, $response);
        $this->assertSame(ActionResponse::REDIRECT_PREVIOUS, $response->getRedirect());
    }
}
