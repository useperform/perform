<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\TypeRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Type\DateType;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * DateTypeTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = new TypeRegistry($this->getMock(ContainerInterface::class));
        $this->registry->addType('date', DateType::class);
        $this->config = new TypeConfig($this->registry);
    }

    public function testListContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime();
        $this->config->add('date', [
            'type' => 'date',
        ]);
        $options = $this->config->getTypes(TypeConfig::CONTEXT_LIST)['date']['listOptions'];

        $expected = $obj->date->format('d/m/Y');
        $this->assertSame($expected, $this->registry->getType('date')->listContext($obj, 'date', $options));
    }

    public function testViewContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime();
        $this->config->add('date', [
            'type' => 'date',
        ]);
        $options = $this->config->getTypes(TypeConfig::CONTEXT_VIEW)['date']['viewOptions'];

        $expected = $obj->date->format('d/m/Y');
        $this->assertSame($expected, $this->registry->getType('date')->viewContext($obj, 'date', $options));
    }
}
