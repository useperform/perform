<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\IntegerType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class IntegerTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'integer' => new IntegerType(),
        ];
    }

    public function testViewContext()
    {
        $obj = new \stdClass();
        $obj->amount = 1001;

        $this->config->add('amount', [
            'type' => 'integer',
        ]);
        $this->assertTrimmedString('1001', $this->viewContext($obj, 'amount'));
    }
}
