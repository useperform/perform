<?php

namespace Perform\RichContentBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Test\TestKernel;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class CompilerTest extends TestCase
{
    protected $kernel;

    private function configure(array $extraConfig = [])
    {
        $this->kernel = new TestKernel([
            new \Perform\RichContentBundle\PerformRichContentBundle(),
            new \Perform\MediaBundle\PerformMediaBundle()
        ], array_merge([__DIR__.'/aliases.yml'], $extraConfig));
        $this->kernel->boot();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    private function getRegistry()
    {
        return $this->kernel->getContainer()->get('test.perform_rich_content.block_type_registry');
    }

    public function testConfigureBlockTypes()
    {
        $this->configure([__DIR__.'/block_types.yml']);
        $types = $this->getRegistry()->all();
        $this->assertSame(['text', 'image'], array_keys($types));
    }

    public function testDefaultAllBlockTypes()
    {
        $this->configure();
        $types = $this->getRegistry()->all();
        $this->assertFalse(empty($types), 'The list of default registered block types must not be empty.');
    }
}
