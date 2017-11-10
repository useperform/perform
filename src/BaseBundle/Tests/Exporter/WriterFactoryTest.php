<?php

namespace Perform\BaseBundle\Tests\Exporter;

use Perform\BaseBundle\Exporter\WriterFactory;
use Perform\BaseBundle\Config\ExportConfig;
use Exporter\Writer\JsonWriter;
use Exporter\Writer\CsvWriter;
use Exporter\Writer\XlsWriter;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class WriterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->factory = new WriterFactory();
    }

    private function property($object, $property)
    {
        $prop = (new \ReflectionObject($object))->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($object);
    }

    public function testCreateDefaultJson()
    {
        $writer = $this->factory->create(ExportConfig::FORMAT_JSON);
        $this->assertInstanceOf(JsonWriter::class, $writer);
    }

    public function testCreateDefaultCsv()
    {
        $writer = $this->factory->create(ExportConfig::FORMAT_CSV);
        $this->assertInstanceOf(CsvWriter::class, $writer);
        $this->assertSame(',', $this->property($writer, 'delimiter'));
        $this->assertSame('"', $this->property($writer, 'enclosure'));
        $this->assertSame('\\', $this->property($writer, 'escape'));
        $this->assertSame(true, $this->property($writer, 'showHeaders'));
        $this->assertSame(false, $this->property($writer, 'withBom'));
    }

    public function testCreateCsvWithOptions()
    {
        $writer = $this->factory->create(ExportConfig::FORMAT_CSV, [
            'delimiter' => ':',
            'enclosure' => "'",
            'escape' => ',',
            'showHeaders' => false,
            'withBom' => true,
        ]);
        $this->assertInstanceOf(CsvWriter::class, $writer);
        $this->assertSame(':', $this->property($writer, 'delimiter'));
        $this->assertSame("'", $this->property($writer, 'enclosure'));
        $this->assertSame(',', $this->property($writer, 'escape'));
        $this->assertSame(false, $this->property($writer, 'showHeaders'));
        $this->assertSame(true, $this->property($writer, 'withBom'));
    }

    public function testCreateDefaultXls()
    {
        $writer = $this->factory->create(ExportConfig::FORMAT_XLS);
        $this->assertInstanceOf(XlsWriter::class, $writer);
        $this->assertSame(true, $this->property($writer, 'showHeaders'));
    }

    public function testCreateXlsWithOptions()
    {
        $writer = $this->factory->create(ExportConfig::FORMAT_XLS, [
            'showHeaders' => false,
        ]);
        $this->assertInstanceOf(XlsWriter::class, $writer);
        $this->assertSame(false, $this->property($writer, 'showHeaders'));
    }
}
