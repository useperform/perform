<?php

namespace Base\Tests\Util;

use Perform\Base\Util\StringUtil;

/**
 * StringUtilTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StringUtilTest extends \PHPUnit_Framework_TestCase
{
    public function sensibleProvider()
    {
        return [
            ['password', 'Password'],
            ['user-id', 'User id'],
            ['EmailAddress', 'Email address'],
            ['date_format', 'Date format'],
            ['_save', 'Save'],
            ['_save_', 'Save'],
        ];
    }

    /**
     * @dataProvider sensibleProvider()
     */
    public function testSensibleLabelString($string, $expected)
    {
        $this->assertSame($expected, StringUtil::sensible($string));
    }
}
