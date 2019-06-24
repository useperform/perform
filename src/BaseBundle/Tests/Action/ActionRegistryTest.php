<?php

namespace Perform\BaseBundle\Tests\Action;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ActionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Action\ActionNotFoundException;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRegistryTest extends TestCase
{
    protected $actionOne;
    protected $actionTwo;
    protected $registry;

    public function setUp()
    {
        $this->actionOne = $this->createMock(ActionInterface::class);
        $this->actionTwo = $this->createMock(ActionInterface::class);
        $this->registry = new ActionRegistry(Services::serviceLocator([
            'one' => $this->actionOne,
            'two' => $this->actionTwo,
        ]));
    }

    public function testGet()
    {
        $this->assertSame($this->actionOne, $this->registry->get('one'));
        $this->assertSame($this->actionTwo, $this->registry->get('two'));
    }

    public function testGetUnknown()
    {
        $this->expectException(ActionNotFoundException::class);
        $this->registry->get('unknown');
    }
}
