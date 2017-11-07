<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Config\ExportConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExportConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testAddField()
    {
        $c = new ExportConfig();
        $this->assertSame($c, $c->addField('Name', 'name'));
        $this->assertSame(['Name' => 'name'], $c->getFields());
    }

    public function testRemoveField()
    {
        $c = new ExportConfig();
        $this->assertSame($c, $c->addField('Name', 'name'));
        $this->assertSame($c, $c->removeField('Name'));
        $this->assertSame([], $c->getFields());
    }

    public function testRemoveFieldNotAdded()
    {
        $c = new ExportConfig();
        $this->assertSame($c, $c->removeField('Name'));
        $this->assertSame([], $c->getFields());
    }

    public function testSetFields()
    {
        $c = new ExportConfig();
        $this->assertSame($c, $c->setFields(['Title' => 'title']));
        $this->assertSame(['Title' => 'title'], $c->getFields());
        $this->assertSame($c, $c->setFields([]));
        $this->assertSame([], $c->getFields());
    }
}
