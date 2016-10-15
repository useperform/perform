<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\TypeConfig;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * TypeConfigTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $config;

    public function setUp()
    {
        $this->config = new TypeConfig();
    }

    public function testGetNoTypes()
    {
        $this->assertSame([], $this->config->getTypes(TypeConfig::CONTEXT_VIEW));
    }

    public function testAddSimpleType()
    {
        $this->assertSame($this->config, $this->config->add('title', [
            'type' => 'string',
        ]));
        $types = $this->config->getTypes(TypeConfig::CONTEXT_CREATE);
        $this->assertArrayHasKey('type', $types['title']);
    }

    public function testTypeMustBeSupplied()
    {
        $this->setExpectedException(MissingOptionsException::class);
        $this->config->add('title', []);
    }

    public function testFieldsCanBeRestrictedToAContext()
    {
        $this->config->add('title', [
            'type' => 'string',
            'contexts' => [
                TypeConfig::CONTEXT_LIST,
                TypeConfig::CONTEXT_VIEW,
            ],
        ]);

        $this->assertSame(1, count($this->config->getTypes(TypeConfig::CONTEXT_VIEW)));
        $this->assertSame(1, count($this->config->getTypes(TypeConfig::CONTEXT_LIST)));
        $this->assertSame(0, count($this->config->getTypes(TypeConfig::CONTEXT_CREATE)));
        $this->assertSame(0, count($this->config->getTypes(TypeConfig::CONTEXT_EDIT)));
    }
}
