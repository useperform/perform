<?php

namespace Admin\Base\Tests\Entity;

use Admin\Base\Entity\Setting;

/**
 * SettingTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingTest extends \PHPUnit_Framework_TestCase
{
    public function testKeyIsSet()
    {
        $setting = new Setting('foo');
        $this->assertSame('foo', $setting->getKey());
    }

    public function testDoesNotRequireUpdateWhenEmpty()
    {
        $setting = new Setting('foo');
        $this->assertFalse($setting->requiresUpdate(new Setting('foo')));
    }

    public function testDoesNotRequireUpdateWhenKeysAreDifferent()
    {
        $setting = new Setting('foo');
        $this->assertFalse($setting->requiresUpdate(new Setting('bar')));
    }

    public function testDoesNotRequireUpdateWhenValuesAreDifferent()
    {
        $existing = new Setting('foo');
        $existing->setValue('foo existing');
        $new = new Setting('foo');
        $new->setValue('foo new');
        $this->assertFalse($existing->requiresUpdate($new));
    }

    public function testRequiresUpdateWhenFieldsHaveChanged()
    {
        $existing = (new Setting('foo'))
                  ->setGlobal(false)
                  ->setRequiredRole('ROLE_ADMIN')
                  ->setType('string')
                  ->setDefaultValue('foo default');

        $new = (new Setting('foo'))
             ->setGlobal(true)
             ->setRequiredRole('ROLE_ADMIN')
             ->setType('string')
             ->setDefaultValue('foo default');
        $this->assertTrue($existing->requiresUpdate($new));

        $new = (new Setting('foo'))
             ->setGlobal(false)
             ->setRequiredRole('ROLE_SUPER_ADMIN')
             ->setType('string')
             ->setDefaultValue('foo default');
        $this->assertTrue($existing->requiresUpdate($new));

        $new = (new Setting('foo'))
             ->setGlobal(false)
             ->setRequiredRole('ROLE_SUPER_ADMIN')
             ->setType('varchar')
             ->setDefaultValue('foo default');
        $this->assertTrue($existing->requiresUpdate($new));

        $new = (new Setting('foo'))
             ->setGlobal(false)
             ->setRequiredRole('ROLE_ADMIN')
             ->setType('string')
             ->setDefaultValue('foo new default');
        $this->assertTrue($existing->requiresUpdate($new));
    }

    public function testUpdate()
    {
        $existing = (new Setting('foo'))
                  ->setGlobal(false)
                  ->setRequiredRole('ROLE_ADMIN')
                  ->setType('string')
                  ->setValue('foo value')
                  ->setDefaultValue('foo default');
        $new = (new Setting('foo'))
             ->setGlobal(true)
             ->setRequiredRole('ROLE_SUPER_ADMIN')
             ->setType('varchar')
             ->setValue('foo new value')
             ->setDefaultValue('foo new default');
        $existing->update($new);

        $this->assertSame(true, $existing->isGlobal());
        $this->assertSame('ROLE_SUPER_ADMIN', $existing->getRequiredRole());
        $this->assertSame('varchar', $existing->getType());
        $this->assertSame('foo new default', $existing->getDefaultValue());
        //value shouldn't have changed
        $this->assertSame('foo value', $existing->getValue());
    }

    public function testExceptionThrownWhenUpdatingDifferentKey()
    {
        $this->setExpectedException('\InvalidArgumentException');
        (new Setting('foo'))->update(new Setting('bar'));
    }

    public function illegalKeyProvider()
    {
        return [
            ['FOO'],
            ['bar-bar'],
            ['bundle.something'],
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
        $this->setExpectedException('\InvalidArgumentException');
        new Setting($key);
    }
}
