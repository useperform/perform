<?php

namespace Perform\BaseBundle\Config;

/**
 * ConfigStoreInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ConfigStoreInterface
{
    /**
     * Get the TypeConfig for an entity. The type config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return TypeConfig
     */
    public function getTypeConfig($entity);

    /**
     * Get the ActionConfig for an entity. The action config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return ActionConfig
     */
    public function getActionConfig($entity);

    /**
     * Get the FilterConfig for an entity. The filter config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return FilterConfig
     */
    public function getFilterConfig($entity);

    /**
     * Get the LabelConfig for an entity. The label config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return LabelConfig
     */
    public function getLabelConfig($entity);
}
