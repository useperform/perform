<?php

namespace Perform\BaseBundle\Action;

/**
 * ActionInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ActionInterface
{
    /**
     * @return ActionResponse
     */
    public function run(array $entities, array $options);

    /**
     * @return bool
     */
    public function isGranted($entity);

    /**
     * @return array
     */
    public function getDefaultConfig();
}
