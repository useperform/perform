<?php

namespace Perform\BaseBundle\Tests\Settings;

use Perform\BaseBundle\Settings\Manager\DoctrineManager;
use Perform\BaseBundle\Repository\SettingRepository;
use Perform\BaseBundle\Exception\SettingNotFoundException;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Perform\BaseBundle\Settings\Manager\WriteableSettingsManagerInterface;
use Perform\BaseBundle\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;

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
        $this->em = $this->getMock(EntityManagerInterface::class);

        $this->manager = new DoctrineManager($this->repo, $this->em);
    }

    public function testImplementsInterfaces()
    {
        $this->assertInstanceOf(SettingsManagerInterface::class, $this->manager);
        $this->assertInstanceOf(WriteableSettingsManagerInterface::class, $this->manager);
    }

    public function testGetValue()
    {
        $s = new Setting('some_setting');
        $s->setValue('some_value');
        $this->repo->expects($this->once())
            ->method('findSetting')
            ->with('some_setting')
            ->will($this->returnValue($s));

        $this->assertSame('some_value', $this->manager->getValue('some_setting'));
    }

    public function testGetDefaultValue()
    {
        $this->repo->expects($this->once())
            ->method('findSetting')
            ->with('some_setting')
            ->will($this->returnValue(null));

        $this->assertSame('some_default', $this->manager->getValue('some_setting', 'some_default'));
    }

    public function testSetValueNotInDatabase()
    {
        $this->repo->expects($this->any())
            ->method('findSetting')
            ->with('some_setting')
            ->will($this->returnValue(null));
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($s) { return $s instanceof Setting && $s->getKey() === 'some_setting' && $s->getValue() === 'value';}));
        $this->em->expects($this->once())
            ->method('flush');

        $this->manager->setValue('some_setting', 'value');
    }

    public function testSetValueInDatabase()
    {
        $s = new Setting('some_setting');
        $s->setValue('value');
        $this->repo->expects($this->any())
            ->method('findSetting')
            ->with('some_setting')
            ->will($this->returnValue($s));
        $this->em->expects($this->once())
            ->method('persist')
            ->with($s);
        $this->em->expects($this->once())
            ->method('flush');

        $this->manager->setValue('some_setting', 'new_value');
        $this->assertSame('new_value', $s->getValue());
    }
}
