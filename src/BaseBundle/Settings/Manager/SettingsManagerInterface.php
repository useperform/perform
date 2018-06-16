<?php

namespace Perform\BaseBundle\Settings\Manager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface SettingsManagerInterface
{
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValue($key, $default = null);

    /**
     * @return mixed
     */
    public function getRequiredValue($key);

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setValue($key, $value);
}
