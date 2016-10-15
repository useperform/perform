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

    public function testThereAreDefaults()
    {
        $this->config->add('title', ['type' => 'string']);
        $type = $this->config->getTypes(TypeConfig::CONTEXT_LIST)['title'];
        $this->assertInternalType('array', $type['options']);
        $this->assertInternalType('string', $type['options']['label']);
    }


    public function testSuppliedOptionsAreReturned()
    {
        $this->config->add('date', [
            'type' => 'datetime',
            'options' => [
                'human' => true,
            ],
        ]);

        $this->assertSame(true, $this->config->getTypes(TypeConfig::CONTEXT_VIEW)['date']['options']['human']);
    }

    public function contextProvider()
    {
        return [
            [TypeConfig::CONTEXT_LIST, 'listOptions'],
            [TypeConfig::CONTEXT_VIEW, 'viewOptions'],
            [TypeConfig::CONTEXT_CREATE, 'createOptions'],
            [TypeConfig::CONTEXT_EDIT, 'editOptions'],
        ];
    }

    /**
     * @dataProvider contextProvider
     */
    public function testOptionsAreOverriddenPerContext($context, $key)
    {
        $this->config->add('date', [
            'type' => 'datetime',
            'options' => [
                'human' => true,
            ],
            $key => [
                'human' => false,
            ],
        ]);

        $notContext = $context === TypeConfig::CONTEXT_VIEW ? TypeConfig::CONTEXT_LIST : TypeConfig::CONTEXT_VIEW;
        $this->assertSame(true, $this->config->getTypes($notContext)['date']['options']['human']);

        $this->assertSame(false, $this->config->getTypes($context)['date']['options']['human']);
    }

    public function testSensibleLabelIsGiven()
    {
        $this->config->add('superTitle', ['type' => 'string']);
        $this->assertSame('Super title', $this->config->getTypes(TypeConfig::CONTEXT_LIST)['superTitle']['options']['label']);
    }

    public function testLabelCanBeOverridden()
    {
        $this->config->add('superTitle', [
            'type' => 'string',
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->assertSame('Title', $this->config->getTypes(TypeConfig::CONTEXT_LIST)['superTitle']['options']['label']);
    }

    public function testLabelCanBeOverriddenPerContext()
    {
        $this->config->add('superTitle', [
            'type' => 'string',
            'listOptions' => [
                'label' => 'Title',
            ],
        ]);
        $this->assertSame('Title', $this->config->getTypes(TypeConfig::CONTEXT_LIST)['superTitle']['options']['label']);
        $this->assertSame('Super title', $this->config->getTypes(TypeConfig::CONTEXT_EDIT)['superTitle']['options']['label']);
    }
}
