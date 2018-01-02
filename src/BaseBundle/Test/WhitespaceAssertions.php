<?php

namespace Perform\BaseBundle\Test;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
trait WhitespaceAssertions
{
    public static function assertTrimmedString($expected, $actual, $message = '')
    {
        self::assertThat($actual, new EqualTrimmedString($expected), $message);
    }
}
