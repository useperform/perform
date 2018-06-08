<?php

namespace Perform\BaseBundle\Config;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ConfigStoreInterface
{
    /**
     * Get the FieldConfig for a crud name.
     *
     * @param string $crudName
     *
     * @return FieldConfig
     */
    public function getFieldConfig($crudName);

    /**
     * Get the ActionConfig for a crud name.
     *
     * @param string $crudName
     *
     * @return ActionConfig
     */
    public function getActionConfig($crudName);

    /**
     * Get the FilterConfig for a crud name.
     *
     * @param string $crudName
     *
     * @return FilterConfig
     */
    public function getFilterConfig($crudName);

    /**
     * Get the LabelConfig for a crud name.
     *
     * @param string $crudName
     *
     * @return LabelConfig
     */
    public function getLabelConfig($crudName);

    /**
     * Get the resolved entity class for a crud name.
     *
     * @param string $crudName
     *
     * @return string
     */
    public function getEntityClass($crudName);
}
