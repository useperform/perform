<?php

namespace Perform\BaseBundle\Test;

/**
 * ComposerConfigTestCase.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class ComposerConfigTestCase extends \PHPUnit_Framework_TestCase
{
    abstract protected function getPackageName();

    abstract protected function getNamespace();

    protected function getBundleConfig()
    {
        $bundleDir = dirname((new \ReflectionObject($this))->getFileName());

        $config = json_decode(file_get_contents($bundleDir.'/../composer.json'), true);
        if ($config === null) {
            throw new \Exception('Unable to load bundle composer.json: '.json_last_error_msg());
        }

        return $config;
    }

    protected function getParentConfig()
    {
        return json_decode(file_get_contents(__DIR__.'/../../../composer.json'), true);
    }

    public function testName()
    {
        $this->assertSame($this->getPackageName(), $this->getBundleConfig()['name']);
    }

    public function testLicense()
    {
        $this->assertSame('proprietary', $this->getBundleConfig()['license']);
    }

    public function testAutoload()
    {
        //namespaces must ends with a backslash to be psr4 compatible
        $namespace = trim($this->getNamespace(), '\\').'\\';
        $this->assertSame([$namespace => ''], $this->getBundleConfig()['autoload']['psr-4']);
    }

    public function testReplaceIsDefinedInParentConfig()
    {
        $parentConfig = $this->getParentConfig();
        $name = $this->getPackageName();
        $this->assertArrayHasKey($name, $parentConfig['replace'], sprintf('Parent composer.json does not replace bundle "%s"', $name));
        $this->assertSame('self.version', $parentConfig['replace'][$name], 'Parent composer.json must use self.version when replacing bundles');
    }

    public function testRequireVersionsMatch()
    {
        $bundleConfig = $this->getBundleConfig();
        $parentConfig = $this->getParentConfig();

        $bundleDeps = isset($bundleConfig['require']) ? $bundleConfig['require'] : [];
        $parentDeps = $parentConfig['require'];

        //check that every dependency is in the parent config, except for other perform bundles
        foreach ($bundleDeps as $dep => $version) {
            if (substr($dep, 0, 8) === 'perform/') {
                $this->assertSame('self.version', $version, 'Version of another perform bundle dependency must be "self.version"');
                continue;
            }

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
