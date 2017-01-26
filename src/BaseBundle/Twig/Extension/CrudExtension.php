<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Admin\AdminRegistry;

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
            new \Twig_SimpleFunction('perform_crud_list_context', [$this, 'listContext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('perform_crud_view_context', [$this, 'viewContext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('perform_crud_entity_name', [$this, 'entityName']),
        ];
    }

    public function listContext($entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->listContext($entity, $field, $config['options']);

        if (in_array(TypeConfig::CONTEXT_LIST, $type->getHtmlContexts())) {
            return $value;
        }

        //check how twig does this
        return htmlspecialchars($value);
    }

    public function viewContext($entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->viewContext($entity, $field, $config['options']);

        if (in_array(TypeConfig::CONTEXT_VIEW, $type->getHtmlContexts())) {
            return $value;
        }

        //check how twig does this
        return htmlspecialchars($value);
    }

    public function entityName($entity)
    {
        return $this->adminRegistry->getAdminForEntity($entity)->getNameForEntity($entity);
    }

    public function getName()
    {
        return 'crud';
    }
}
