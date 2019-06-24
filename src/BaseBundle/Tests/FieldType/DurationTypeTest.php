<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\Test\WhitespaceAssertions;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\FieldType\DurationType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DurationTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    public function registerTypes()
    {
        return [
            'duration' => new DurationType(),
        ];
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $obj->duration = 3600;
        $this->config->add('duration', [
            'type' => 'duration',
            'options' => [
                'format' => DurationType::FORMAT_VERBOSE,
            ]
        ]);

        $this->assertTrimmedString('1 hour', $this->listContext($obj, 'duration'));
    }

    public function testViewContext()
    {
        $obj = new \stdClass();
        $obj->duration = 67564;
        $this->config->add('duration', [
            'type' => 'duration',
            'viewOptions' => [
                'format' => DurationType::FORMAT_DIGITAL,
            ]
        ]);

        $this->assertTrimmedString('18:46:04', $this->viewContext($obj, 'duration'));
    }
}
