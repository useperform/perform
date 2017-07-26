<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Config\LabelConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LabelConfigTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = new LabelConfig();
    }

    public function testDefaults()
    {
        $this->assertSame('', $this->config->getEntityName());
        $this->assertSame('', $this->config->getEntityNamePlural());
        $this->assertSame('', $this->config->getEntityLabel(new \stdClass()));
    }

    public function testLabel()
    {
        $entity = new \stdClass();
        $entity->prop = 'foo';
        $this->assertSame($this->config, $this->config->setEntityLabel(function($e) { return $e->prop; }));
        $this->assertSame('foo', $this->config->getEntityLabel($entity));
    }

    public function testGetName()
    {
        $this->assertSame($this->config, $this->config->setEntityName('Box'));
        $this->assertSame('Box', $this->config->getEntityName());
    }

    public function testGetNamePlural()
    {
        $this->assertSame($this->config, $this->config->setEntityName('Box'));
        $this->assertSame('Boxes', $this->config->getEntityNamePlural());
    }

    public function testGetNamePluralSetExplicitly()
    {
        $this->assertSame($this->config, $this->config->setEntityNamePlural('Group of boxes'));
        $this->assertSame('Group of boxes', $this->config->getEntityNamePlural());
    }
}
