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

    protected $fields = [];
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

    /**
     * @param string $label
     * @param string $field
     *
     * @return ExportConfig
     */
    public function addField($label, $field)
    {
        $this->fields[$label] = $field;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return ExportConfig
     */
    public function removeField($label)
    {
        if (isset($this->fields[$label])) {
            unset($this->fields[$label]);
        }

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return ExportConfig
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
