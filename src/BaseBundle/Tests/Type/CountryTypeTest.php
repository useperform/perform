<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\CountryType;
use Perform\BaseBundle\Test\TypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class CountryTypeTest extends TypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'country' => new CountryType(),
        ];
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $obj->homeCountry = 'GB';
        $config = [
            'type' => 'country',
            'template' => '@PerformBase/type/simple.html.twig',
            'listOptions' => [],
        ];
        $this->assertTrimmedString('United Kingdom', $this->renderer->listContext($obj, 'homeCountry', $config));
    }
}
