<?php

namespace Perform\BaseBundle\Tests\Command;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Perform\BaseBundle\Command\BundleFilter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleFilterTest extends \PHPUnit_Framework_TestCase
{
    private function mockBundle($name)
    {
        $bundle = $this->getMock(BundleInterface::class);
        $bundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $bundle;
    }

    private function mockInput(array $onlyBundles = [], array $excludeBundles = [])
    {
        $definition = new InputDefinition([
            new InputOption('only-bundles', 'o', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED),
            new InputOption('exclude-bundles', 'x', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED),
        ]);

        return new ArrayInput([
            '--only-bundles' => $onlyBundles,
            '--exclude-bundles' => $excludeBundles,
        ], $definition);
    }

    public function testFilterNoOptions()
    {
        $bundles = [
            $this->mockBundle('FooBundle'),
            $this->mockBundle('BarBundle'),
        ];

        $this->assertSame($bundles, BundleFilter::filterBundles($this->mockInput(), $bundles));
    }

    public function testFilterNamesNoOptions()
    {
        $bundles = [
            $this->mockBundle('FooBundle'),
            $this->mockBundle('BarBundle'),
        ];

        $this->assertSame(['FooBundle', 'BarBundle'], BundleFilter::filterBundleNames($this->mockInput(), $bundles));
    }

    public function testFilterOnlyBundles()
    {
        $bundles = [
            $foo = $this->mockBundle('FooBundle'),
            $bar = $this->mockBundle('BarBundle'),
            $baz = $this->mockBundle('BazBundle'),
        ];

        $input = $this->mockInput(['foo', 'bazbundle']);
        $this->assertSame([$foo, $baz], BundleFilter::filterBundles($input, $bundles));
    }

    public function testFilterNamesOnlyBundles()
    {
        $bundles = [
            $this->mockBundle('FooBundle'),
            $this->mockBundle('BarBundle'),
            $this->mockBundle('BazBundle'),
        ];

        $input = $this->mockInput(['foo', 'bazbundle']);
        $this->assertSame(['FooBundle', 'BazBundle'], BundleFilter::filterBundleNames($input, $bundles));
    }

    public function testFilterExcludeBundles()
    {
        $bundles = [
            $foo = $this->mockBundle('FooBundle'),
            $bar = $this->mockBundle('BarBundle'),
            $baz = $this->mockBundle('BazBundle'),
        ];

        $input = $this->mockInput([], ['foo', 'bazbundle']);
        $this->assertSame([$bar], BundleFilter::filterBundles($input, $bundles));
    }

    public function testFilterNamesExcludeBundles()
    {
        $bundles = [
            $this->mockBundle('FooBundle'),
            $this->mockBundle('BarBundle'),
            $this->mockBundle('BazBundle'),
        ];

        $input = $this->mockInput([], ['foo', 'bazbundle']);
        $this->assertSame(['BarBundle'], BundleFilter::filterBundleNames($input, $bundles));
    }

    public function testOnlyHasPriority()
    {
        $bundles = [
            $foo = $this->mockBundle('FooBundle'),
            $bar = $this->mockBundle('BarBundle'),
            $baz = $this->mockBundle('BazBundle'),
        ];

        $input = $this->mockInput(['foo'], ['foo', 'barbundle']);
        $this->assertSame([$foo], BundleFilter::filterBundles($input, $bundles));
    }
}
