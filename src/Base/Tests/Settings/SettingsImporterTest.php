<?php

namespace Perform\Base\Tests\Settings;

use Perform\Base\Settings\SettingsImporter;
use Perform\Base\Entity\Setting;

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
            ->with('PerformBaseBundle:Setting')
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

    public function testParseYamlFile()
    {
        $settings = $this->importer->parseYamlFile(__DIR__.'/sample_settings.yml');
        $this->assertSame(2, count($settings));

        $one = $settings[0];
        $this->assertSame('setting_one', $one->getKey());
        $this->assertSame('string', $one->getType());
        $this->assertTrue($one->isGlobal());

        $two = $settings[1];
        $this->assertSame('setting_two', $two->getKey());
        $this->assertSame('boolean', $two->getType());
        $this->assertFalse($two->isGlobal());
        $this->assertSame(true, $two->getDefaultValue());
        $this->assertSame('ROLE_ADMIN', $two->getRequiredRole());
    }

    public function testImportYamlFile()
    {
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');
        $this->importer->importYamlFile(__DIR__.'/sample_settings.yml');
    }
}
