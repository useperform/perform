<?php

namespace Perform\BaseBundle\Tests;

/**
 * ComposerConfigTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends \PHPUnit_Framework_TestCase
{
    protected function getBundleConfig()
    {
        return json_decode(file_get_contents(__DIR__.'/../composer.json'), true);
    }

    protected function getParentConfig()
    {
        return json_decode(file_get_contents(__DIR__.'/../../../composer.json'), true);
    }

    public function testName()
    {
        $this->assertSame('perform/base-bundle', $this->getBundleConfig()['name']);
    }

    public function testLicense()
    {
        $this->assertSame('proprietary', $this->getBundleConfig()['license']);
    }

    public function testAutoload()
    {
        //remove Tests
        $namespace = substr(__NAMESPACE__, 0, -5);
        $this->assertSame([$namespace => ''], $this->getBundleConfig()['autoload']['psr-4']);
    }

    public function testRequireVersionsMatch()
    {
        $bundleConfig = $this->getBundleConfig();
        $parentConfig = $this->getParentConfig();

        $bundleDeps = isset($bundleConfig['require']) ? $bundleConfig['require'] : [];
        $parentDeps = $parentConfig['require'];

        foreach ($bundleDeps as $dep => $version) {
            $this->assertArrayHasKey($dep, $parentDeps, sprintf('Parent composer.json does not contain dependency "%s"', $dep));
            $this->assertSame($parentDeps[$dep], $version, sprintf('Required version of "%s" does not match parent composer.json', $dep));
        }
    }

    public function testRequireDevVersionsMatch()
    {
        $bundleConfig = $this->getBundleConfig();
        $parentConfig = $this->getParentConfig();

        $bundleDeps = isset($bundleConfig['require-dev']) ? $bundleConfig['require-dev'] : [];
        $parentDeps = $parentConfig['require-dev'];

        foreach ($bundleDeps as $dep => $version) {
            $this->assertArrayHasKey($dep, $parentDeps, sprintf('Parent composer.json does not contain dev dependency "%s"', $dep));
            $this->assertSame($parentDeps[$dep], $version, sprintf('Required dev version of "%s" does not match parent composer.json', $dep));
        }
    }
}
