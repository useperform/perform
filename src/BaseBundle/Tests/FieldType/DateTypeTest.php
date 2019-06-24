<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\FieldType\DateType;
use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Test\Services;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'date' => new DateType(),
        ];
    }

    public function testListContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime('2015/12/12');
        $this->config->add('date', [
            'type' => 'date',
        ]);
        $this->assertTrimmedString('12/12/2015', $this->listContext($obj, 'date'));
    }

    public function testViewContextWithDefaults()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime('2018/01/31');
        $this->config->add('date', [
            'type' => 'date',
        ]);
        $this->assertTrimmedString('31/01/2018', $this->viewContext($obj, 'date'));
    }

    public function testViewContextWithTimezone()
    {
        $obj = new \stdClass();
        $obj->date = new \DateTime('2018/01/31', new \DateTimeZone('UTC'));
        $this->config->add('date', [
            'type' => 'date',
            'options' => [
                'view_timezone' => 'Pacific/Tahiti'
            ]
        ]);
        $this->assertTrimmedString('30/01/2018', $this->viewContext($obj, 'date'));
    }
}
