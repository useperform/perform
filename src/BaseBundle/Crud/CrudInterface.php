<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\LabelConfig;
use Perform\BaseBundle\Config\ExportConfig;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface CrudInterface
{
    public function configureTypes(TypeConfig $config);

    public function configureFilters(FilterConfig $config);

    public function configureActions(ActionConfig $config);

    public function configureLabels(LabelConfig $config);

    /**
     * @param ExportConfig $config
     *
     * Configure how exports work for this entity.
     * You may wish to set the available formats and configure how they behave.
     */
    public function configureExports(ExportConfig $config);

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
     * Get the name of the template for the given entity and context.
     *
     * The supplied twig environment may be used to check if templates exist.
     *
     * @param Environment $twig
     * @param string      $entityName
     * @param string      $context
     *
     * @return string
     */
    public function getTemplate(Environment $twig, $entityName, $context);
}
