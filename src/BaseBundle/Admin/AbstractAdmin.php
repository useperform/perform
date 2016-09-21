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
    protected $listFields = [];
    protected $viewFields = [];
    protected $createFields = [];
    protected $editFields = [];
    protected $fieldOptions = [];
    protected $listFieldOptions = [];
    protected $viewFieldOptions = [];
    protected $createFieldOptions = [];
    protected $editFieldOptions = [];
    protected $routePrefix;

    public function configure(array $options)
    {
        $properties = [
            'fieldOptions' => &$this->fieldOptions,
            'listFieldOptions' => &$this->listFieldOptions,
            'viewFieldOptions' => &$this->viewFieldOptions,
            'createFieldOptions' => &$this->createFieldOptions,
            'editFieldOptions' => &$this->editFieldOptions,
        ];

        foreach ($properties as $key => &$property) {
            if (!isset($options[$key])) {
                continue;
            }
            foreach ($options[$key] as $field => $fieldOptions) {
                $property[$field] = array_merge(
                    isset($property[$field]) ? $property[$field] : [],
                    $fieldOptions);
            }
        }
    }

    public function getListFields()
    {
        return $this->resolveFields($this->listFields, $this->listFieldOptions);
    }

    public function getViewFields()
    {
        return $this->resolveFields($this->viewFields, $this->viewFieldOptions);
    }

    public function getCreateFields()
    {
        return $this->resolveFields($this->createFields, $this->createFieldOptions);
    }

    public function getEditFields()
    {
        return $this->resolveFields($this->editFields, $this->editFieldOptions);
    }

    protected function resolveFields(array $fields, array $overrideFields)
    {
        $results = [];
        foreach ($fields as $field) {
            $options = isset($this->fieldOptions[$field]) ? $this->fieldOptions[$field] : [];
            if (isset($overrideFields[$field])) {
                $options = array_merge($options, $overrideFields[$field]);
            }

            if (!isset($options['type'])) {
                $options['type'] = 'string';
            }

            $results[$field] = $options;
        }

        return $results;
    }

    public function getFormType()
    {
        return AdminType::CLASS;
    }

    public function getRoutePrefix()
    {
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
