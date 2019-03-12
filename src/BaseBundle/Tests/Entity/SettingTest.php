<?php

namespace Perform\BaseBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Entity\Setting;
use Perform\UserBundle\Entity\User;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingTest extends TestCase
{
    public function testKeyIsSet()
    {
        $setting = new Setting('foo');
        $this->assertSame('foo', $setting->getKey());
    }

    public function illegalKeyProvider()
    {
        return [
            ['  '],
            [''],
            [[]],
        ];
    }

    /**
     * @dataProvider illegalKeyProvider
     */
    public function testKeyCannotContainIllegalCharacters($key)
    {
        $this->expectException('\InvalidArgumentException');
        new Setting($key);
    }

    public function testSetUser()
    {
        $user = new User();
        $setting = new Setting('key');
        $this->assertSame($setting, $setting->setUser($user));
        $this->assertSame($user, $setting->getUser());
    }

    public function testGetNullValue()
    {
        $setting = new Setting('key');
        $this->assertNull($setting->getValue());
    }


    public function valuesProvider()
    {
        return [
            [true],
            [false],
            ['value'],
            [['list', 'of', 'values']],
            [new \stdClass],
            [[new \DateTime, new \DateTime]],
        ];
    }

    /**
     * @dataProvider valuesProvider
     */
    public function testGetSetValue($value)
    {
        $setting = new Setting('key');
        $this->assertSame($setting, $setting->setValue($value));
        $this->assertEquals($value, $setting->getValue());
    }

    /**
     * e.g. when doctrine retrieves an invalid value from the database
     */
    public function testInvalidValueIsUnserializedToNull()
    {
        $setting = new Setting('key');
        $prop = (new \ReflectionObject($setting))->getProperty('value');
        $prop->setAccessible(true);
        $prop->setValue($setting, 'invalid serialized value');
        $this->assertNull($setting->getValue());
    }

    public function testFalseValueIsNotConvertedToNull()
    {
        $setting = new Setting('key');
        $setting->setValue(false);
        $this->assertSame(false, $setting->getValue());
    }
}
