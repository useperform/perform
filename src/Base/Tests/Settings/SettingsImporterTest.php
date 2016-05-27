<?php

namespace Admin\Base\Tests\Settings;

use Admin\Base\Settings\SettingsImporter;
use Admin\Base\Entity\Setting;

/**
 * SettingsImporterTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsImporterTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $repo;
    protected $importer;

    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $this->repo = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->with('AdminBaseBundle:Setting')
            ->will($this->returnValue($this->repo));
        $this->importer = new SettingsImporter($this->entityManager);
    }

    public function testImportNewSetting()
    {
        $setting = new Setting('test_key');
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($setting);
        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->importer->import($setting);
    }

    public function testExistingSettingStopsImport()
    {
        $existing = new Setting('test_key');
        $new = new Setting('test_key');
        $this->entityManager->expects($this->never())
            ->method('persist');
        $this->entityManager->expects($this->never())
            ->method('flush');
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with(['key' => 'test_key'])
            ->will($this->returnValue($existing));

        $this->importer->import($new);
    }

    public function testExistingSettingIsUpdated()
    {
        $existing = new Setting('test_key');
        $existing->setValue('existing_value')
            ->setGlobal(false);
        $new = new Setting('test_key');
        $new->setValue('new_value')
            ->setGlobal(true);
        $this->assertTrue($existing->requiresUpdate($new));

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existing);
        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with(['key' => 'test_key'])
            ->will($this->returnValue($existing));

        $this->importer->import($new);
        $this->assertFalse($existing->requiresUpdate($new));
        $this->assertSame('existing_value', $existing->getValue());
    }
}
