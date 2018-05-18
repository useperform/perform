<?php

namespace Perform\BaseBundle\Tests\Action;

use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Action\DeleteAction;
use Perform\BaseBundle\Action\ActionResponse;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DeleteActionTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $action;

    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->action = new DeleteAction($this->em);
    }

    public function testEntitiesAreDeleted()
    {
        $one = new \stdClass();
        $two = new \stdClass();
        $this->em->expects($this->exactly(2))
            ->method('remove')
            ->with($this->logicalOr($one, $two));
        $this->em->expects($this->once())
            ->method('flush');

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
