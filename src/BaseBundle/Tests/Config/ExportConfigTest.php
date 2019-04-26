<?php

namespace Perform\BaseBundle\Tests\Config;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Config\ExportConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExportConfigTest extends TestCase
{
    public function testGetDefaultFilename()
    {
        $config = new ExportConfig();
        $this->assertSame('data.json', $config->getFilename(ExportConfig::FORMAT_JSON));
        $this->assertSame('data.csv', $config->getFilename(ExportConfig::FORMAT_CSV));
        $this->assertSame('data.xls', $config->getFilename(ExportConfig::FORMAT_XLS));
    }

    public function testGetStringFilename()
    {
        $config = new ExportConfig();
        $config->setFilename('secret_info');
        $this->assertSame('secret_info.json', $config->getFilename(ExportConfig::FORMAT_JSON));
        $this->assertSame('secret_info.csv', $config->getFilename(ExportConfig::FORMAT_CSV));
        $this->assertSame('secret_info.xls', $config->getFilename(ExportConfig::FORMAT_XLS));
    }

    public function testGetFunctionFilename()
    {
        $config = new ExportConfig();
        $obj = new \stdClass();
        $obj->i = 0;
        $config->setFilename(function($format) use ($obj) {
            $obj->i++;
            return $obj->i.$format;
        });
        $this->assertSame('1json', $config->getFilename(ExportConfig::FORMAT_JSON));
        $this->assertSame('2csv', $config->getFilename(ExportConfig::FORMAT_CSV));
        $this->assertSame('3xls', $config->getFilename(ExportConfig::FORMAT_XLS));
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
