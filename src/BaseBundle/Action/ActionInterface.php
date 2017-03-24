<?php

namespace Perform\BaseBundle\Action;

use Perform\BaseBundle\Admin\AdminRequest;

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
     * @return bool
     */
    public function isAvailable(AdminRequest $request);

    /**
     * @return array
     */
    public function getDefaultConfig();
}
