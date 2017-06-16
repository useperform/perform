<?php

namespace Perform\BaseBundle\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\AdminType;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;

/**
 * AbstractAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractAdmin implements AdminInterface
{
    protected $routePrefix;

    public function getFormType()
    {
        return AdminType::CLASS;
    }

    public function getRoutePrefix()
    {
        if (!$this->routePrefix) {
            throw new \Exception('An admin route prefix must be configured.');
        }

        return $this->routePrefix;
    }

    public function getControllerName()
    {
        return 'Perform\BaseBundle\Controller\CrudController';
    }

    public function getActions()
    {
        return [
            '/' => 'list',
            '/view/{id}' => 'view',
            '/create' => 'create',
            '/edit/{id}' => 'edit',
        ];
    }

    public function getNameForEntity($entity)
    {
        $pieces = explode('\\', get_class($entity));
        $class = end($pieces);
        return sprintf('%s %s', $class, $entity->getId());
    }

    public function configureFilters(FilterConfig $config)
    {
    }

    public function configureActions(ActionConfig $config)
    {
        $config->add('perform_base_delete');
    }
}
