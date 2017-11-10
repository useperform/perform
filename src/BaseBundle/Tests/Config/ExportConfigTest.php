<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Config\ExportConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExportConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFilename()
    {
        $config = new ExportConfig();
        $this->assertSame('data.csv', $config->getFilename(ExportConfig::FORMAT_CSV));
    }

    public function testSetFormats()
    {
        $config = new ExportConfig();
        $formats = [
            ExportConfig::FORMAT_CSV,
            ExportConfig::FORMAT_JSON,
            ExportConfig::FORMAT_XLS,
        ];
        $this->assertSame($config, $config->setFormats($formats));
        $this->assertSame($formats, $config->getFormats());
    }

    public function testConfigureKnownFormat()
    {
        $config = new ExportConfig();
        $config->setFormats([ExportConfig::FORMAT_CSV]);
        $this->assertSame($config, $config->configureFormat(ExportConfig::FORMAT_CSV, [
            'showHeaders' => false,
        ]));

        $expected = [
            ExportConfig::FORMAT_CSV => [
                'showHeaders' => false,
            ]
        ];
        $this->assertSame($expected, $config->getFormatOptions());
    }

    public function testConfigureUnknownFormat()
    {
        $config = new ExportConfig();
        $this->assertSame($config, $config->configureFormat('foo', [
            'bar' => true,
        ]));

        $this->assertSame([], $config->getFormatOptions());
    }

    public function testUnconfiguredFormats()
    {
        $config = new ExportConfig();
        $this->assertSame($config, $config->setFormats([
            ExportConfig::FORMAT_CSV,
            ExportConfig::FORMAT_JSON,
            ExportConfig::FORMAT_XLS,
        ]));

        $expected = [
            ExportConfig::FORMAT_CSV => [],
            ExportConfig::FORMAT_JSON => [],
            ExportConfig::FORMAT_XLS => [],
        ];
        $this->assertSame($expected, $config->getFormatOptions());
    }
}
