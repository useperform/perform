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

    protected $availableFormats = [];

    public function __construct()
    {
    }

    /**
     * @param array $availableFormats
     *
     * @return ExportConfig
     */
    public function setFormats(array $availableFormats)
    {
        $this->availableFormats = $availableFormats;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return $this->availableFormats;
    }

    public function getFilename($format)
    {
        return 'data.'.$format;
    }

    public function getFields()
    {
        return ['id'];
    }
}
