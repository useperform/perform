<?php

namespace Perform\BaseBundle\Config;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\Action\ActionRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * ConfigStore creates and stores a single instance of the different
 * config classes for each crud class.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigStore implements ConfigStoreInterface
{
    protected $resolver;
    protected $crudRegistry;
    protected $typeRegistry;
    protected $actionRegistry;
    protected $authChecker;

    protected $typeConfigs = [];
    protected $filterConfigs = [];
    protected $actionConfigs = [];
    protected $labelConfigs = [];
    protected $entityClasses = [];

    public function __construct(EntityResolver $resolver, CrudRegistry $crudRegistry, FieldTypeRegistry $typeRegistry, ActionRegistry $actionRegistry, AuthorizationCheckerInterface $authChecker)
    {
        $this->resolver = $resolver;
        $this->crudRegistry = $crudRegistry;
        $this->typeRegistry = $typeRegistry;
        $this->actionRegistry = $actionRegistry;
        $this->authChecker = $authChecker;
    }

    public function getTypeConfig($crudName)
    {
        if (!isset($this->typeConfigs[$crudName])) {
            $typeConfig = new TypeConfig($this->typeRegistry);
            $this->crudRegistry->get($crudName)->configureTypes($typeConfig);

            $this->typeConfigs[$crudName] = $typeConfig;
        }

        return $this->typeConfigs[$crudName];
    }

    public function getActionConfig($crudName)
    {
        if (!isset($this->actionConfigs[$crudName])) {
            $this->actionConfigs[$crudName] = new ActionConfig($this->actionRegistry, $this->authChecker, $crudName);
            $this->crudRegistry->get($crudName)->configureActions($this->actionConfigs[$crudName]);
        }

        return $this->actionConfigs[$crudName];
    }

    public function getFilterConfig($crudName)
    {
        if (!isset($this->filterConfigs[$crudName])) {
            $this->filterConfigs[$crudName] = new FilterConfig();
            $this->crudRegistry->get($crudName)->configureFilters($this->filterConfigs[$crudName]);
        }

        return $this->filterConfigs[$crudName];
    }

    public function getLabelConfig($crudName)
    {
        if (!isset($this->labelConfigs[$crudName])) {
            $this->labelConfigs[$crudName] = new LabelConfig();
            $this->crudRegistry->get($crudName)->configureLabels($this->labelConfigs[$crudName]);
        }

        return $this->labelConfigs[$crudName];
    }

    public function getExportConfig($crudName)
    {
        if (!isset($this->exportConfigs[$crudName])) {
            $this->exportConfigs[$crudName] = new ExportConfig();
            $this->exportConfigs[$crudName]->setFormats([
                ExportConfig::FORMAT_JSON,
                ExportConfig::FORMAT_CSV,
                ExportConfig::FORMAT_XLS,
            ]);
            $this->crudRegistry->get($crudName)->configureExports($this->exportConfigs[$crudName]);
        }

        return $this->exportConfigs[$crudName];
    }

    public function getEntityClass($crudName)
    {
        if (!isset($this->entityClasses[$crudName])) {
            $crudClass = get_class($this->crudRegistry->get($crudName));
            $this->entityClasses[$crudName] = $this->resolver->resolve($crudClass::getEntityClass());
        }

        return $this->entityClasses[$crudName];
    }
}
