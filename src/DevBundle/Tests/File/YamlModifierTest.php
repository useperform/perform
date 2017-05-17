<?php

namespace Perform\DevBundle\Tests\File;

use Perform\DevBundle\File\YamlModifier;
use Perform\DevBundle\BundleResource\ContactBundleResource;
use Perform\DevBundle\BundleResource\MediaBundleResource;

/**
 * YamlModifierTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class YamlModifierTest extends \PHPUnit_Framework_TestCase
{
    protected $baseFile;
    protected $testFile;
    protected $modifiedFile;

    public function setUp()
    {
        $this->baseFile = __DIR__.'/../Fixtures/routing.yml';
        $this->testFile = __DIR__.'/../Fixtures/test_routing.yml';
        $this->modifiedFile = __DIR__.'/../Fixtures/modified_routing.yml';
        copy($this->baseFile, $this->testFile);
    }

    public function tearDown()
    {
        unlink($this->testFile);
    }

    public function testAddYaml()
    {
        $this->modifier = new YamlModifier($this->testFile);
        $this->modifier->addConfig((new MediaBundleResource)->getRoutes());
        $this->modifier->addConfig((new ContactBundleResource)->getRoutes());
        $this->assertFileEquals($this->modifiedFile, $this->testFile);
    }

    public function testBundleIsNotAddedMoreThanOnce()
    {
        $this->modifier = new YamlModifier($this->testFile);
        $this->modifier->addConfig((new MediaBundleResource)->getRoutes());
        $this->modifier->addConfig((new ContactBundleResource)->getRoutes());

        $this->modifier->addConfig((new MediaBundleResource)->getRoutes());
        $this->modifier->addConfig((new ContactBundleResource)->getRoutes());
        $this->assertFileEquals($this->modifiedFile, $this->testFile);
    }

    public function testCustomCheckPattern()
    {
        $this->modifier = new YamlModifier($this->testFile);

        $this->modifier->addConfig((new ContactBundleResource)->getRoutes(), '/^perform_base_dashboard:/m');
        $this->assertFileEquals($this->baseFile, $this->testFile);
    }
}
