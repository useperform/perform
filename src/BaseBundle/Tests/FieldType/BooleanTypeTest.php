<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\FieldType\BooleanType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BooleanTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    public function registerTypes()
    {
        return [
            'boolean' => new BooleanType(),
        ];
    }

    public function testListContext()
    {
        $this->config->add('enabled', [
            'type' => 'boolean',
        ]);

        $obj = new \stdClass();

        $obj->enabled = true;
        $this->assertTrimmedString('Yes', $this->listContext($obj, 'enabled'));

        $obj->enabled = false;
        $this->assertTrimmedString('No', $this->listContext($obj, 'enabled'));
    }

    public function testViewContext()
    {
        $this->config->add('enabled', [
            'type' => 'boolean',
            'options' => [
                'value_labels' => ['Y', 'N'],
            ],
        ]);

        $obj = new \stdClass();

        $obj->enabled = true;
        $this->assertTrimmedString('Y', $this->viewContext($obj, 'enabled'));

        $obj->enabled = false;
        $this->assertTrimmedString('N', $this->viewContext($obj, 'enabled'));
    }
}
