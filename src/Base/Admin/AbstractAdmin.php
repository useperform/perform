<?php

namespace Admin\Base\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Admin\Base\Form\Type\AdminType;

/**
 * AbstractAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractAdmin implements AdminInterface
{
    protected $listFields = [];
    protected $viewFields = [];
    protected $createFields = [];
    protected $editFields = [];
    protected $routePrefix;

    public function getListFields()
    {
        return $this->listFields;
    }

    public function getViewFields()
    {
        return $this->viewFields;
    }

    public function getCreateFields()
    {
        return $this->createFields;
    }

    public function getEditFields()
    {
        return $this->editFields;
    }

    public function getFormType()
    {
        return AdminType::CLASS;
    }

    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }
}
