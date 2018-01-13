<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\CountryType;
use Perform\BaseBundle\Test\TypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CountryTypeTest extends TypeTestCase
{
    use WhitespaceAssertions;

    protected function configure()
    {
        $this->typeRegistry->addType('country', CountryType::class);
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
