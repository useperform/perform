<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Admin\AdminRegistry;
use Symfony\Component\Form\FormView;

/**
 * CrudExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $typeRegistry;
    protected $adminRegistry;

    public function __construct(CrudUrlGenerator $urlGenerator, TypeRegistry $typeRegistry, AdminRegistry $adminRegistry)
    {
        $this->urlGenerator = $urlGenerator;
        $this->typeRegistry = $typeRegistry;
        $this->adminRegistry = $adminRegistry;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_crud_route', [$this->urlGenerator, 'generate']),
            new \Twig_SimpleFunction('perform_crud_route_exists', [$this->urlGenerator, 'routeExists']),
            new \Twig_SimpleFunction('perform_crud_list_context', [$this, 'listContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_view_context', [$this, 'viewContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_create_context', [$this, 'createContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_edit_context', [$this, 'editContext'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_crud_entity_name', [$this, 'entityName']),
        ];
    }

    public function listContext(\Twig_Environment $twig, $entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->listContext($entity, $field, $config['listOptions']);
        $vars = is_array($value) ? $value : ['value' => $value];
        $template = $type->getTemplate();

        return $twig->loadTemplate($template)->renderBlock('list', $vars);
    }

    public function viewContext(\Twig_Environment $twig, $entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->viewContext($entity, $field, $config['viewOptions']);
        $vars = is_array($value) ? $value : ['value' => $value];
        $template = $type->getTemplate();

        return $twig->loadTemplate($template)->renderBlock('view', $vars);
    }

    public function createContext(\Twig_Environment $twig, $entity, $field, array $config, FormView $form)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $template = $type->getTemplate();
        //type vars are anything returned from the createContext() method call
        $typeVars = isset($form->vars['type_vars'][$field]) ? $form->vars['type_vars'][$field] : [];
        $vars = [
            'field' => $field,
            'form' => $form,
            'entity' => $entity,
            'context' => TypeConfig::CONTEXT_CREATE,
            'type_vars' => $typeVars,
        ];

        return $twig->loadTemplate($template)->renderBlock('create', $vars);
    }

    public function editContext(\Twig_Environment $twig, $entity, $field, array $config, FormView $form)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $template = $type->getTemplate();
        //type vars are anything returned from the editContext() method call
        $typeVars = isset($form->vars['type_vars'][$field]) ? $form->vars['type_vars'][$field] : [];
        $vars = [
            'field' => $field,
            'form' => $form,
            'entity' => $entity,
            'context' => TypeConfig::CONTEXT_EDIT,
            'type_vars' => $typeVars,
        ];

        return $twig->loadTemplate($template)->renderBlock('edit', $vars);
    }

    public function entityName($entity)
    {
        return $this->adminRegistry->getAdmin($entity)->getNameForEntity($entity);
    }

    public function getName()
    {
        return 'crud';
    }
}
