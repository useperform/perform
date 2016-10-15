<?php

namespace Perform\BaseBundle\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\AdminType;

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
            '/delete/{id}' => 'delete',
        ];
    }
}
