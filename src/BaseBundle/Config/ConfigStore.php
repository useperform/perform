<?php

namespace Perform\BaseBundle\Config;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Action\ActionRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * ConfigStore creates and stores a single instance of the different
 * config classes for each entity.
 *
 * Config classes are configured by the entity admin, and may also be
 * overridden by configuration passed to the ConfigStore.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigStore implements ConfigStoreInterface
{
    protected $resolver;
    protected $adminRegistry;
    protected $typeRegistry;
    protected $actionRegistry;
    protected $authChecker;
    protected $override;

    protected $typeConfigs = [];
    protected $filterConfigs = [];
    protected $actionConfigs = [];
    protected $labelConfigs = [];

    public function __construct(EntityResolver $resolver, AdminRegistry $adminRegistry, TypeRegistry $typeRegistry, ActionRegistry $actionRegistry, AuthorizationCheckerInterface $authChecker, array $override = [])
    {
        $this->resolver = $resolver;
        $this->adminRegistry = $adminRegistry;
        $this->typeRegistry = $typeRegistry;
        $this->actionRegistry = $actionRegistry;
        $this->authChecker = $authChecker;
        $this->override = $override;
    }

    public function getTypeConfig($entity)
    {
        $class = $this->resolver->resolve($entity);
        if (!isset($this->typeConfigs[$class])) {
            $typeConfig = new TypeConfig($this->typeRegistry);
            $this->adminRegistry->getAdmin($class)->configureTypes($typeConfig);

            if (isset($this->override[$class]['types'])) {
                foreach ($this->override[$class]['types'] as $field => $config) {
                    $typeConfig->add($field, $config);
                }
            }
            $this->typeConfigs[$class] = $typeConfig;
        }

        return $this->typeConfigs[$class];
    }

    public function getActionConfig($entity)
    {
        $class = $this->resolver->resolve($entity);
        if (!isset($this->actionConfigs[$class])) {
            $this->actionConfigs[$class] = new ActionConfig($this->actionRegistry, $this->authChecker);
            $this->adminRegistry->getAdmin($class)->configureActions($this->actionConfigs[$class]);
        }

        return $this->actionConfigs[$class];
    }

    public function getFilterConfig($entity)
    {
        $class = $this->resolver->resolve($entity);
        if (!isset($this->filterConfigs[$class])) {
            $this->filterConfigs[$class] = new FilterConfig();
            $this->adminRegistry->getAdmin($class)->configureFilters($this->filterConfigs[$class]);
        }

        return $this->filterConfigs[$class];
    }

    public function getLabelConfig($entity)
    {
        $class = $this->resolver->resolve($entity);
        if (!isset($this->labelConfigs[$class])) {
            $this->labelConfigs[$class] = new LabelConfig();
            $this->adminRegistry->getAdmin($class)->configureLabels($this->labelConfigs[$class]);
        }

        return $this->labelConfigs[$class];
    }
}
