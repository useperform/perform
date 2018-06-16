<?php

namespace Perform\BaseBundle\Tests\Settings;

use Perform\BaseBundle\Settings\Manager\DoctrineManager;
use Perform\BaseBundle\Repository\SettingRepository;
use Perform\BaseBundle\Exception\SettingNotFoundException;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DoctrineManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->repo = $this->getMockBuilder(SettingRepository::class)
                    ->disableOriginalConstructor()
                    ->getMock();

        $this->manager = new DoctrineManager($this->repo);
    }

    public function testImplementsInterfaces()
    {
        $this->assertInstanceOf(SettingsManagerInterface::class, $this->manager);
    }

    public function testGetValue()
    {
        $this->repo->expects($this->once())
            ->method('getRequiredValue')
            ->with('some_setting')
            ->will($this->returnValue('some_value'));

        $this->assertSame('some_value', $this->manager->getValue('some_setting'));
    }

    public function testGetDefaultValue()
    {
        $this->repo->expects($this->once())
            ->method('getRequiredValue')
            ->with('some_setting')
            ->will($this->throwException(new SettingNotFoundException()));

        $this->assertSame('some_default', $this->manager->getValue('some_setting', 'some_default'));
    }

    public function testSetValue()
    {
        $this->repo->expects($this->once())
            ->method('setValue')
            ->with('some_setting', 'new_value');

        $this->manager->setValue('some_setting', 'new_value');
    }
}
