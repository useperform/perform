<?php

namespace Perform\BaseBundle\Config;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ConfigStoreInterface
{
    /**
     * Get the TypeConfig for a crud name. The type config may include
     * overrides from application configuration.
     *
     * @param string|object $crudName
     *
     * @return TypeConfig
     */
    public function getTypeConfig($crudName);

    /**
     * Get the ActionConfig for a crud name. The action config may include
     * overrides from application configuration.
     *
     * @param string|object $crudName
     *
     * @return ActionConfig
     */
    public function getActionConfig($crudName);

    /**
     * Get the FilterConfig for a crud name. The filter config may include
     * overrides from application configuration.
     *
     * @param string|object $crudName
     *
     * @return FilterConfig
     */
    public function getFilterConfig($crudName);

    /**
     * Get the LabelConfig for a crud name. The label config may include
     * overrides from application configuration.
     *
     * @param string|object $crudName
     *
     * @return LabelConfig
     */
    public function getLabelConfig($crudName);

    /**
     * Get the resolved entity class for a crud name.
     */
    public function getEntityClass($crudName);
}
