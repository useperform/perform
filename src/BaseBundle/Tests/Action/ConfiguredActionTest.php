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
        $ca = new ConfiguredAction('foo', $this->action, function() {}, function() {});
        $this->assertSame('foo', $ca->getName());
    }

    public function testGetLabel()
    {
        $ca = new ConfiguredAction('foo', $this->action, function($entity) { return $entity->id; }, function() {});
        $entity = new \stdClass();
        $entity->id = 1;
        $this->assertSame(1, $ca->getLabel($entity));
    }

    public function testGetBatchLabel()
    {
        $ca = new ConfiguredAction('foo', $this->action, function() {}, function() { return 'batch';});
        $this->assertSame('batch', $ca->getBatchLabel());
    }
}
