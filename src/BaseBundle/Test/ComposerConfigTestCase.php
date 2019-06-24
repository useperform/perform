<?php

namespace Perform\BaseBundle\Test;

use PHPUnit\Framework\TestCase;

abstract class ComposerConfigTestCase extends TestCase
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

    public function testName()
    {
        $this->assertSame($this->getPackageName(), $this->getBundleConfig()['name']);
        $this->assertSame('perform/', substr($this->getPackageName(), 0, 8));
    }

    public function testLicense()
    {
        $this->assertSame('proprietary', $this->getBundleConfig()['license']);
    }

    public function testAutoload()
    {
        //namespaces must end with a backslash to be psr4 compatible
        $namespace = trim($this->getNamespace(), '\\').'\\';
        $this->assertSame([$namespace => ''], $this->getBundleConfig()['autoload']['psr-4']);
    }
}
