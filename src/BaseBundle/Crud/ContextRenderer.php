<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Symfony\Component\Form\FormView;
use Perform\BaseBundle\Config\FieldConfig;

/**
 * Render data and forms for entities, using types and crud classes.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContextRenderer
{
    protected $typeRegistry;
    protected $twig;

    public function __construct(FieldTypeRegistry $typeRegistry, \Twig_Environment $twig)
    {
        $this->typeRegistry = $typeRegistry;
        $this->twig = $twig;
    }

    /**
     * Render HTML for an entity property in the 'list' context.
     */
    public function listContext($entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $vars = $type->listContext($entity, $field, $config['listOptions']);
        if (!is_array($vars)) {
            throw new \UnexpectedValueException(sprintf('%s#listContext must return an array.', get_class($type)));
        }
        $vars = array_merge([
            'entity' => $entity,
            'field' => $field,
        ], $vars);

        return $this->twig->loadTemplate($config['template'])->renderBlock('list', $vars);
    }

    /**
     * Render HTML for an entity property in the 'view' context.
     */
    public function viewContext($entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $typeVars = $type->viewContext($entity, $field, $config['viewOptions']);
        if (!is_array($typeVars)) {
            throw new \UnexpectedValueException(sprintf('%s#viewContext must return an array.', get_class($type)));
        }
        $vars = array_merge([
            'entity' => $entity,
            'field' => $field,
        ], $typeVars);

        return $this->twig->loadTemplate($config['template'])->renderBlock('view', $vars);
    }

    /**
     * Render form fields and HTML for an entity property in the 'create' context.
     */
    public function createContext($entity, $field, array $config, FormView $form)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $template = $config['template'];
        //type vars are anything returned from the createContext() method call
        $typeVars = isset($form->vars['type_vars'][$field]) ? $form->vars['type_vars'][$field] : [];
        $vars = [
            'field' => $field,
            'form' => $form,
            'label' => $config['createOptions']['label'],
            'entity' => $entity,
            'context' => CrudRequest::CONTEXT_CREATE,
            'type_vars' => $typeVars,
        ];

        return $this->twig->loadTemplate($template)->renderBlock('create', $vars);
    }

    /**
     * Render form fields and HTML for an entity property in the 'edit' context.
     */
    public function editContext($entity, $field, array $config, FormView $form)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $template = $config['template'];
        //type vars are anything returned from the editContext() method call
        $typeVars = isset($form->vars['type_vars'][$field]) ? $form->vars['type_vars'][$field] : [];
        $vars = [
            'field' => $field,
            'form' => $form,
            'label' => $config['editOptions']['label'],
            'entity' => $entity,
            'context' => CrudRequest::CONTEXT_EDIT,
            'type_vars' => $typeVars,
        ];

        return $this->twig->loadTemplate($template)->renderBlock('edit', $vars);
    }
}
