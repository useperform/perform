<?php

namespace BaseBundle\Tests\Util;

use Perform\BaseBundle\Util\StringUtil;

/**
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

    public function previewProvider()
    {
        return [
            ['', ''],
            ['This is a short sentence.', 'This is a short sentence.'],
            ['This is a long sentence, with a lot of words and letters. It will surely have to be shortened.', 'This is a long sentence, with a lot of words and l…'],
        ];
    }

    /**
     * @dataProvider previewProvider()
     */
    public function testPreviewString($string, $expected)
    {
        $this->assertSame($expected, StringUtil::preview($string));
    }

    public function adminClassProvider()
    {
        return [
            ['AppBundle\Admin\TestAdmin', 'Test'],
            ['AppBundle\Admin\MySuperEntityAdmin', 'My Super Entity'],
            ['AppBundle\Admin\HTMLEntityAdmin', 'HTML Entity'],
        ];
    }

    /**
     * @dataProvider adminClassProvider()
     */
    public function testAdminClassToEntityName($class, $expected)
    {
        $this->assertSame($expected, StringUtil::adminClassToEntityName($class));
    }
}
