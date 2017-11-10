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

    protected $formats = [
        'Json' => self::FORMAT_JSON,
        'CSV' => self::FORMAT_CSV,
        'Excel' => self::FORMAT_XLS,
    ];

    public function __construct()
    {
    }

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

    public function getFilename($format)
    {
        return 'data.'.$format;
    }
}
