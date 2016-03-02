<?php

namespace Admin\Base\Admin;

use Symfony\Component\Form\FormBuilderInterface;

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

    public function getListFields()
    {
        return $this->listFields;
    }

    public function getViewFields()
    {
        return $this->viewFields;
    }

    public function buildCreateForm(FormBuilderInterface $builder, $entity)
    {
        foreach ($this->createFields as $label => $field) {
            $builder->add($field);
        }
    }
}
