<?php

namespace Perform\BaseBundle\Config;

/**
 * ConfigStoreInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ConfigStoreInterface
{
    public function getTypeConfig($entity);

    public function getActionConfig($entity);

    public function getFilterConfig($entity);
}
