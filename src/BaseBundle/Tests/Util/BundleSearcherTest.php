<?php

namespace Perform\BaseBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Util\BundleSearcher;
use Perform\BaseBundle\Tests\MockKernel;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Perform\BaseBundle\Tests\Fixtures\FirstBundle\FirstBundle;
use Perform\BaseBundle\Tests\Fixtures\SecondBundle\SecondBundle;
use Symfony\Component\Finder\Finder;

/**
 * BundleSearcherTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleSearcherTest extends TestCase
{
    protected $kernel;
    protected $searcher;
    protected $bundles = [];

    public function setUp()
    {
        $this->kernel = new MockKernel();
        $this->searcher = new BundleSearcher($this->kernel);
    }

    protected function bundle($name)
    {
        $bundle = $this->createMock(BundleInterface::class);
        $bundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->kernel->addBundle($bundle);

        return $bundle;
    }

    public function testGetAllBundles()
    {
        $foo = $this->bundle('FooBundle');
        $bar = $this->bundle('BarBundle');
        $expected = [
            'FooBundle' => $foo,
            'BarBundle' => $bar,
        ];
        $this->assertSame($expected, $this->searcher->getBundles([]));
    }

    public function testGetOneBundle()
    {
        $foo = $this->bundle('FooBundle');
        $bar = $this->bundle('BarBundle');
        $expected = [
            'FooBundle' => $foo,
        ];
        $this->assertSame($expected, $this->searcher->getBundles(['FooBundle']));
    }

    public function testGetOneBundleFromInstance()
    {
        $foo = $this->bundle('FooBundle');
        $bar = $this->bundle('BarBundle');
        $expected = [
            'FooBundle' => $foo,
        ];
        $this->assertSame($expected, $this->searcher->getBundles([$foo]));
    }

    public function testGetManyBundles()
    {
        $foo = $this->bundle('FooBundle');
        $bar = $this->bundle('BarBundle');
        $baz = $this->bundle('BazBundle');
        $expected = [
            'FooBundle' => $foo,
            'BazBundle' => $baz,
        ];
        $this->assertSame($expected, $this->searcher->getBundles(['FooBundle', 'BazBundle']));
    }

    public function testGetManyBundlesFromInstances()
    {
        $foo = $this->bundle('FooBundle');
        $bar = $this->bundle('BarBundle');
        $baz = $this->bundle('BazBundle');
        $expected = [
            'FooBundle' => $foo,
            'BazBundle' => $baz,
        ];
        $this->assertSame($expected, $this->searcher->getBundles([$foo, $baz]));
    }

    public function testGetManyBundlesFromNameAndInstance()
    {
        $foo = $this->bundle('FooBundle');
        $bar = $this->bundle('BarBundle');
        $baz = $this->bundle('BazBundle');
        $expected = [
            'FooBundle' => $foo,
            'BazBundle' => $baz,
        ];
        $this->assertSame($expected, $this->searcher->getBundles([$foo, 'BazBundle']));
    }

    public function testFindResourcesAllBundles()
    {
        $this->kernel->addBundle(new FirstBundle());
        $this->kernel->addBundle(new SecondBundle());
        $this->bundle('NoResourcesBundle');

        $finder = $this->searcher->findResourcesAtPath('config/services.yml');
        $this->assertInstanceOf(Finder::class, $finder);
        $files = iterator_to_array($finder->getIterator());
        $expected = [
            realpath(__DIR__.'/../Fixtures/FirstBundle/Resources/config/services.yml'),
            realpath(__DIR__.'/../Fixtures/SecondBundle/Resources/config/services.yml'),
        ];
        $this->assertSame($expected, array_keys($files));
    }

    public function testFindResourcesOneBundle()
    {
        $this->kernel->addBundle(new FirstBundle());
        $this->kernel->addBundle(new SecondBundle());

        $finder = $this->searcher->findResourcesAtPath('config/services.yml', ['SecondBundle']);
        $this->assertInstanceOf(Finder::class, $finder);
        $files = iterator_to_array($finder->getIterator());
        $expected = [
            realpath(__DIR__.'/../Fixtures/SecondBundle/Resources/config/services.yml'),
        ];
        $this->assertSame($expected, array_keys($files));
    }

    public function testFindClassesAllBundles()
    {
        $this->kernel->addBundle(new FirstBundle());
        $this->kernel->addBundle(new SecondBundle());

        $expected = [
            "Perform\BaseBundle\Tests\Fixtures\FirstBundle\Service\Something",
            "Perform\BaseBundle\Tests\Fixtures\SecondBundle\Service\Something",
        ];

        $this->assertSame($expected, array_values($this->searcher->findClassesWithNamespaceSegment('Service')));
    }

    public function testFindClassesOneBundle()
    {
        $this->kernel->addBundle(new FirstBundle());
        $this->kernel->addBundle(new SecondBundle());

        $expected = [
            "Perform\BaseBundle\Tests\Fixtures\FirstBundle\Service\Something",
        ];

        $this->assertSame($expected, array_values($this->searcher->findClassesWithNamespaceSegment('Service', null, ['FirstBundle'])));
    }

    public function testFindClassesWithMapperAllBundles()
    {
        $this->kernel->addBundle(new FirstBundle());
        $this->kernel->addBundle(new SecondBundle());

        $expected = [
            "Perform\BaseBundle\Tests\Fixtures\FirstBundle\Service\Something" => 'eldnubtsrif Something',
            "Perform\BaseBundle\Tests\Fixtures\SecondBundle\Service\Something" => 'eldnubdnoces Something',
        ];

        $mapper = function ($class, $classBasename, $bundle) {
            return strrev(strtolower($bundle->getName())).' '.$classBasename;
        };

        $this->assertSame($expected, $this->searcher->findClassesWithNamespaceSegment('Service', $mapper));
    }
}
