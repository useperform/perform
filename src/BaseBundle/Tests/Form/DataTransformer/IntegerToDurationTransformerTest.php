<?php

namespace Perform\BaseBundle\Tests\Type;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Form\DataTransformer\IntegerToDurationTransformer;

/**
 * IntegerToDurationTransformerTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class IntegerToDurationTransformerTest extends TestCase
{
    public function setUp()
    {
        $this->transformer = new IntegerToDurationTransformer();
    }

    public function testTransform()
    {
        $this->assertSame('276d 7h 12m 28s', $this->transformer->transform(23872348));
    }

    public function testReverseTransform()
    {
        $this->assertSame(23872348, $this->transformer->reverseTransform('276d 7h 12m 28s'));
    }
}
