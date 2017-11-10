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
        $config->setFormats($formats);
        $this->assertSame($formats, $config->getFormats());
    }
}
