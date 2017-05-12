<?php

namespace Perform\DevBundle\Tests\File;

use Perform\DevBundle\File\RoutingModifier;

/**
 * RoutingModifierTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RoutingModifierTest extends \PHPUnit_Framework_TestCase
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
        $this->modifier = new RoutingModifier($this->testFile);
        $this->modifier->addConfig(RoutingModifier::CONFIGS['PerformMediaBundle']);
        $this->modifier->addConfig(RoutingModifier::CONFIGS['PerformContactBundle']);
        $this->assertFileEquals($this->modifiedFile, $this->testFile);
    }

    public function testBundleIsNotAddedMoreThanOnce()
    {
        $this->modifier = new RoutingModifier($this->testFile);
        $this->modifier->addConfig(RoutingModifier::CONFIGS['PerformMediaBundle']);
        $this->modifier->addConfig(RoutingModifier::CONFIGS['PerformContactBundle']);

        $this->modifier->addConfig(RoutingModifier::CONFIGS['PerformMediaBundle']);
        $this->modifier->addConfig(RoutingModifier::CONFIGS['PerformContactBundle']);
        $this->assertFileEquals($this->modifiedFile, $this->testFile);
    }
}
