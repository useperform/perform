<?php

namespace Perform\BaseBundle\Tests\Settings;

use Perform\BaseBundle\Settings\SettingsManager;
use Perform\BaseBundle\Entity\Setting;

/**
 * SettingsManagerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $this->repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repo));

        $this->manager = new SettingsManager($this->entityManager);
    }

    public function testSetValue()
    {
        $setting = new Setting('foo');
        $setting->setValue('foo value');
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with(['key' => 'foo'])
            ->will($this->returnValue($setting));
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($setting);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->manager->setValue('foo', 'foo new value');
        $this->assertSame('foo new value', $setting->getValue());
    }
}
