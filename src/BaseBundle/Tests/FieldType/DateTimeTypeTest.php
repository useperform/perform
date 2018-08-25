<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\FieldType\DateTimeType;
use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Test\Services;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'datetime' => new DateTimeType(),
        ];
    }

    public function testListContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime();
        $this->config->add('date', [
            'type' => 'datetime',
        ]);
        $this->assertTrimmedString('1 second ago', $this->listContext($obj, 'date'));
    }

    public function testViewContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime('2017-12-12 09:30');
        $this->config->add('date', [
            'type' => 'datetime',
        ]);
        $expected = $obj->date->format('g:ia d/m/Y');
        $this->assertTrimmedString('9:30am 12/12/2017', $this->viewContext($obj, 'date'));
    }

    public function testViewContextWithTimezone()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime('2017-12-12 09:30', new \DateTimeZone('UTC'));
        $this->config->add('date', [
            'type' => 'datetime',
            'options' => [
                'view_timezone' => 'America/Chicago',
            ]
        ]);
        $expected = $obj->date->format('g:ia d/m/Y');
        $this->assertTrimmedString('3:30am 12/12/2017', $this->viewContext($obj, 'date'));
    }
}
