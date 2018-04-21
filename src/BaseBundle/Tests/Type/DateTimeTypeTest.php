<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\DateTimeType;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = Services::typeRegistry([
            'datetime' => new DateTimeType(),
        ]);
        $this->config = new TypeConfig($this->registry);
    }

    public function testListContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime();
        $this->config->add('date', [
            'type' => 'datetime',
        ]);
        $options = $this->config->getTypes(TypeConfig::CONTEXT_LIST)['date']['listOptions'];

        $this->assertSame('1 second ago', $this->registry->getType('datetime')->listContext($obj, 'date', $options));
    }

    public function testViewContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime();
        $this->config->add('date', [
            'type' => 'datetime',
        ]);
        $options = $this->config->getTypes(TypeConfig::CONTEXT_VIEW)['date']['viewOptions'];

        $expected = $obj->date->format('g:ia d/m/Y');
        $this->assertSame($expected, $this->registry->getType('datetime')->viewContext($obj, 'date', $options));
    }
}
