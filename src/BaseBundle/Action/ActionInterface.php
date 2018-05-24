<?php

namespace Perform\BaseBundle\Action;

use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ActionInterface
{
    /**
     * @return ActionResponse
     */
    public function run(CrudRequest $crudRequest, array $entities, array $options);

    /**
     * @return array
     */
    public function getDefaultConfig();
}
