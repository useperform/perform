<?php

namespace Perform\BaseBundle\Tests\Settings;

use Perform\BaseBundle\Settings\Manager\ParametersManager;
use Perform\BaseBundle\Exception\SettingNotFoundException;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ParametersManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->manager = new ParametersManager($this->container);
    }

    public function testImplementsInterfaces()
    {
        $this->assertInstanceOf(SettingsManagerInterface::class, $this->manager);
    }

    public function testGetValue()
    {
        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('some_setting')
            ->will($this->returnValue('some_value'));

        $this->assertSame('some_value', $this->manager->getValue('some_setting'));
    }

    public function testGetDefaultValue()
    {
        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('some_setting')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->assertSame('some_default', $this->manager->getValue('some_setting', 'some_default'));
    }

    public function testGetRequired()
    {
        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('some_setting')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->setExpectedException(SettingNotFoundException::class);
        $this->manager->getRequiredValue('some_setting');
    }

    public function testSetValueNotSupported()
    {
        $this->setExpectedException(\Exception::class);
        $this->manager->setValue('some_setting', 'new_value');
    }
}
