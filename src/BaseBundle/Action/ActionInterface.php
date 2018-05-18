<?php

namespace Perform\BaseBundle\Action;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ActionInterface
{
    /**
     * @return ActionResponse
     */
    public function run(array $entities, array $options);

    /**
     * @return array
     */
    public function getDefaultConfig();
}
