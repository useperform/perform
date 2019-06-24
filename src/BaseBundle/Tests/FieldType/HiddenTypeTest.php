<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\HiddenType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HiddenTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'hidden' => new HiddenType(),
        ];
    }

    public function testViewContext()
    {
        $obj = new \stdClass();
        $obj->secret = 'abc123';

        $this->config->add('secret', [
            'type' => 'hidden',
        ]);
        $this->assertTrimmedString('abc123', $this->viewContext($obj, 'secret'));
    }
}
