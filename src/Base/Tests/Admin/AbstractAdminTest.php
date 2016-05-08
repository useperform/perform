<?php

namespace Admin\Base\Tests\Admin;

use Admin\Base\Admin\UserAdmin;

/**
 * AbstractAdminTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AbstractAdminTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->admin = new TestAdmin();
    }

    public function testIsAdmin()
    {
        $this->assertInstanceOf('Admin\Base\Admin\AdminInterface', $this->admin);
    }

    public function testGetDefaultFields()
    {
        $expected = [
            'one' => [
                'type' => 'string',
            ],
            'two' => [
                'type' => 'string',
            ],
        ];
        $this->assertEquals($expected, $this->admin->getListFields());
    }

    public function testGetFieldsWithOptions()
    {
        $expected = [
            'three' => [
                'type' => 'datetime',
            ],
            'four' => [
                'type' => 'string',
                'label' => 'Special Label',
            ],
        ];
        $this->assertEquals($expected, $this->admin->getViewFields());
    }

    public function testGetFieldsWithContextOptionsMerged()
    {
        $expected = [
            'five' => [
                'type' => 'datetime',
                'label' => 'Label for creation',
            ],
            'six' => [
                'label' => 'Special Label',
                'type' => 'string',
                'foo' => 'bar',
            ],
        ];
        $this->assertEquals($expected, $this->admin->getCreateFields());
    }

    public function testGetFieldsWithContextOptionsOverridingDefault()
    {
        $expected = [
            'seven' => [
                'type' => 'type_for_editing',
            ],
            'eight' => [
                'label' => 'Special Label',
                'type' => 'string',
                'foo' => 'bar',
            ],
        ];
        $this->assertEquals($expected, $this->admin->getEditFields());
    }

    public function testExistingFieldsCanBeConfigured()
    {
        $options = [
            'fieldOptions' => [
                'one' => [
                    'type' => 'text',
                ],
            ],
            'editFieldOptions' => [
                'eight' => [
                    'label' => 'some crazy label',
                ],
            ],
        ];
        $this->admin->configure($options);
        $expected = [
            'one' => [
                'type' => 'text',
            ],
            'two' => [
                'type' => 'string',
            ],
        ];
        $this->assertEquals($expected, $this->admin->getListFields());

        $expected = [
            'seven' => [
                'type' => 'type_for_editing',
            ],
            'eight' => [
                'label' => 'some crazy label',
                'type' => 'string',
                'foo' => 'bar',
            ],
        ];
        $this->assertEquals($expected, $this->admin->getEditFields());
    }
}
