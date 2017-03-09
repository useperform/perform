<?php

namespace Perform\BaseBundle\Admin;

use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Filter\FilterConfig;

/**
 * AdminInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface AdminInterface
{
    public function configureTypes(TypeConfig $config);

    public function configureFilters(FilterConfig $config);

    /**
     * @return string
     */
    public function getFormType();

    /**
     * @return string
     */
    public function getRoutePrefix();

    /**
     * @return string
     */
    public function getControllerName();

    /**
     * @return array
     */
    public function getActions();

    /**
     * Get a readable name for an entity.
     *
     * @return string
     */
    public function getNameForEntity($entity);
}
