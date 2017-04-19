<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Perform\BaseBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * ConfigurationTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = new Configuration();
    }

    protected function process(array $configs)
    {
        $p = new Processor();

        return $p->processConfiguration($this->config, $configs);
    }

    public function testMenuDefaults()
    {
        $config = $this->process([]);
        $expected = [
            'simple' => [],
            'order' => [],
        ];

        $this->assertSame($expected, $config['menu']);
    }
}
