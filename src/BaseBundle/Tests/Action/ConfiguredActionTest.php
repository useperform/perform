<?php

namespace Perform\BaseBundle\Tests\Action;

use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Action\ConfiguredAction;

/**
 * ConfiguredActionTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfiguredActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->action = $this->getMock(ActionInterface::class);
    }

    public function testGetName()
    {
        $ca = new ConfiguredAction('foo', $this->action, []);
        $this->assertSame('foo', $ca->getName());
    }

    public function testGetLabel()
    {
        $options = [
            'label' => function($entity) { return $entity->id; },
        ];
        $ca = new ConfiguredAction('foo', $this->action, $options);
        $entity = new \stdClass();
        $entity->id = 1;
        $this->assertSame(1, $ca->getLabel($entity));
    }

    public function testGetBatchLabel()
    {
        $options = [
            'batchLabel' => function() { return 'batch'; },
        ];
        $ca = new ConfiguredAction('foo', $this->action, $options);
        $this->assertSame('batch', $ca->getBatchLabel());
    }

    public function testIsConfirmationRequired()
    {
        $options = [
            'confirmationRequired' => function() { return true; },
        ];
        $ca = new ConfiguredAction('foo', $this->action, $options);
        $this->assertTrue($ca->isConfirmationRequired());
    }
}
