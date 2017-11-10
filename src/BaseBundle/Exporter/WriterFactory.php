<?php

namespace Perform\BaseBundle\Exporter;

use Exporter\Writer\CsvWriter;
use Exporter\Writer\JsonWriter;
use Exporter\Writer\XlsWriter;
use Perform\BaseBundle\Config\ExportConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class WriterFactory implements WriterFactoryInterface
{
    /**
     * @return WriterInterface
     */
    public function create($format, array $options = [])
    {
        switch ($format) {
        case ExportConfig::FORMAT_JSON:
            return new JsonWriter('php://output');
        case ExportConfig::FORMAT_CSV:
            return new CsvWriter(
                'php://output',
                isset($options['delimiter']) ? $options['delimiter'] : ',',
                isset($options['enclosure']) ? $options['enclosure'] : '"',
                isset($options['escape']) ? $options['escape'] : '\\',
                isset($options['showHeaders']) ? $options['showHeaders'] : true,
                isset($options['withBom']) ? $options['withBom'] : false
            );
        case ExportConfig::FORMAT_XLS:
            return new XlsWriter(
                'php://output',
                isset($options['showHeaders']) ? $options['showHeaders'] : true
            );
        default:
            throw new \InvalidArgumentException(sprintf('Unknown exporter format "%s".', $format));
        }
    }
}
