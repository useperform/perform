<?php

namespace Perform\DevBundle\Tests\File;

use Perform\DevBundle\File\YamlModifier;
use Perform\DevBundle\BundleResource\ContactBundleResource;
use Perform\DevBundle\BundleResource\MediaBundleResource;
use Symfony\Component\Yaml\Yaml;

/**
 * YamlModifierTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class YamlModifierTest extends \PHPUnit_Framework_TestCase
{
    private function routing($file)
    {
        return sprintf('%s/../Fixtures/routing/%s.yml', __DIR__, $file);
    }

    private function configDev($file)
    {
        return sprintf('%s/../Fixtures/config_dev/%s.yml', __DIR__, $file);
    }

    public function tearDown()
    {
        @unlink($this->routing('actual'));
        @unlink($this->configDev('actual'));
    }

    public function testAddYaml()
    {
        copy($this->routing('current'), $this->routing('actual'));

        $mod = new YamlModifier($this->routing('actual'));
        $mod->addConfig((new MediaBundleResource())->getRoutes());
        $mod->addConfig((new ContactBundleResource())->getRoutes());

        $this->assertFileEquals($this->routing('expected'), $this->routing('actual'));
    }

    public function testBundleIsNotAddedMoreThanOnce()
    {
        copy($this->routing('current'), $this->routing('actual'));

        $mod = new YamlModifier($this->routing('actual'));
        $mod->addConfig((new MediaBundleResource())->getRoutes());
        $mod->addConfig((new ContactBundleResource())->getRoutes());
        $mod->addConfig((new MediaBundleResource())->getRoutes());
        $mod->addConfig((new ContactBundleResource())->getRoutes());

        $this->assertFileEquals($this->routing('expected'), $this->routing('actual'));
    }

    public function testCustomCheckPattern()
    {
        copy($this->routing('current'), $this->routing('actual'));

        $mod = new YamlModifier($this->routing('actual'));
        $mod->addConfig((new ContactBundleResource())->getRoutes(), '/^perform_base_dashboard:/m');

        $this->assertFileEquals($this->routing('current'), $this->routing('actual'));
    }

    public function testReplaceSection()
    {
        copy($this->configDev('current_with_section'), $this->configDev('actual'));

        $mod = new YamlModifier($this->configDev('actual'));
        $yaml = Yaml::dump([
            'perform_dev' => [
                'some_val' => 'foo',
                'some_other_val' => ['bar', 'baz'],
                'skeleton_vars' => [
                    'app_name' => 'Super app',
                    'app_description' => 'Awesome',
                ],
            ],
        ], 5);
        $mod->replaceSection('perform_dev', $yaml);

        $this->assertFileEquals($this->configDev('expected_with_section'), $this->configDev('actual'));
    }

    public function testReplaceSectionNotExisting()
    {
        copy($this->configDev('current_no_section'), $this->configDev('actual'));

        $mod = new YamlModifier($this->configDev('actual'));
        $yaml = Yaml::dump([
            'perform_dev' => [
                'some_val' => 'foo',
                'some_other_val' => ['bar', 'baz'],
                'skeleton_vars' => [
                    'app_name' => 'Super app',
                    'app_description' => 'Awesome',
                ],
            ],
        ], 5);
        $mod->replaceSection('perform_dev', $yaml);

        $this->assertFileEquals($this->configDev('expected_no_section'), $this->configDev('actual'));
    }
}
