<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Action\LinkAction;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Action\ActionRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LinkActionTest extends \PHPUnit_Framework_TestCase
{
    protected $authChecker;
    protected $action;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder(ActionRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->authChecker = $this->getMock(AuthorizationCheckerInterface::class);
        $this->config = new ActionConfig($this->registry, $this->authChecker, 'some_crud');
        $this->config->addInstance('link', new LinkAction());
        $this->action = $this->config->get('link');
    }

    private function crudRequest()
    {
        return new CrudRequest('some_crud', CrudRequest::CONTEXT_ACTION);
    }

    public function testRunIsForbidden()
    {
        $this->setExpectedException(\RuntimeException::class);
        $this->action->run($this->crudRequest(), [], []);
    }

    public function testBatchActionDisabled()
    {
        $this->assertFalse($this->action->isBatchOptionAvailable($this->crudRequest()));
    }

    public function testIsAlwaysGranted()
    {
        $this->assertTrue($this->action->isGranted(new \stdClass(), $this->authChecker));
    }
}
