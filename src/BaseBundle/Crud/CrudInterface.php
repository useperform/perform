<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\Config\FieldConfig;
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
    /**
     * @param FieldConfig $config
     *
     * Configure field mappings.
     * For each entity property you want to expose, call $config->add().
     */
    public function configureFields(FieldConfig $config);

    /**
     * @param FilterConfig $config
     *
     * Configure filters.
     */
    public function configureFilters(FilterConfig $config);

    /**
     * @param ActionConfig $config
     *
     * Configure actions.
     */
    public function configureActions(ActionConfig $config);

    /**
     * @param LabelConfig $config
     *
     * Configure labels.
     */
    public function configureLabels(LabelConfig $config);

    /**
     * @param ExportConfig $config
     *
     * Configure how exports work.
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
    public function getControllerName();

    /**
     * Get the name of the template for the given entity and context.
     *
     * The supplied twig environment may be used to check if templates exist.
     *
     * @param Environment $twig
     * @param string      $crudName
     * @param string      $context
     *
     * @return string
     */
    public function getTemplate(Environment $twig, $crudName, $context);

    /**
     * Get the entity class name managed by this crud service.
     *
     * @return string
     */
    public static function getEntityClass();
}
