<?php

namespace Perform\BaseBundle\Admin;

use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Symfony\Component\Templating\EngineInterface;

/**
 * AdminInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface AdminInterface
{
    public function configureTypes(TypeConfig $config);

    public function configureFilters(FilterConfig $config);

    public function configureActions(ActionConfig $config);

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

    /**
     * Get the name of the template for the given entity and context.
     *
     * The supplied templating engine may be used to check if templates exist.
     *
     * @param EngineInterface $templating
     * @param string          $entityName
     * @param string          $context
     *
     * @return string
     */
    public function getTemplate(EngineInterface $templating, $entityName, $context);
}
