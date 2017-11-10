<?php

namespace Perform\BaseBundle\Config;

/**
 * Defines how lists of entities are exported.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExportConfig
{
    const FORMAT_JSON = 'json';
    const FORMAT_CSV = 'csv';
    const FORMAT_XLS = 'xls';

    protected $formats = [];
    protected $formatOptions = [];

    /**
     * @param array $formats
     *
     * @return ExportConfig
     */
    public function setFormats(array $formats)
    {
        $this->formats = $formats;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @param string $format
     * @param array $options
     */
    public function configureFormat($format, array $options)
    {
        $this->formatOptions[$format] = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormatOptions()
    {
        $options = [];
        foreach ($this->formats as $format) {
            $options[$format] = isset($this->formatOptions[$format]) ? $this->formatOptions[$format] : [];
        }

        return $options;
    }

    public function getFilename($format)
    {
        return 'data.'.$format;
    }
}
