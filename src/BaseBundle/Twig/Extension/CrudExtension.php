<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Pagerfanta\View\TwitterBootstrap4View;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Perform\BaseBundle\Crud\ContextRenderer;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * CrudExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension extends \Twig_Extension
{
    protected $renderer;
    protected $store;
    protected $urlGenerator;
    protected $requestStack;

    public function __construct(ContextRenderer $renderer, ConfigStoreInterface $store, CrudUrlGenerator $urlGenerator, RequestStack $requestStack)
    {
        $this->renderer = $renderer;
        $this->store = $store;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_crud_route', [$this->urlGenerator, 'generate']),
            new \Twig_SimpleFunction('perform_crud_route_exists', [$this->urlGenerator, 'routeExists']),
            new \Twig_SimpleFunction('perform_crud_list_context', [$this->renderer, 'listContext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('perform_crud_view_context', [$this->renderer, 'viewContext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('perform_crud_create_context', [$this->renderer, 'createContext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('perform_crud_edit_context', [$this->renderer, 'editContext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('perform_crud_entity_name', [$this, 'entityName']),
            new \Twig_SimpleFunction('perform_crud_entity_label', [$this, 'entityLabel']),
            new \Twig_SimpleFunction('perform_crud_paginator', [$this, 'paginator'], ['is_safe' => ['html']]),
        ];
    }

    public function paginator(Pagerfanta $pagerfanta, $crudName)
    {
        $view = new TwitterBootstrap4View();
        $options = [
            'proximity' => 3,
        ];
        $requestParams = $this->requestStack->getCurrentRequest()->query->all();

        $routeGenerator = function ($page) use ($requestParams, $crudName) {
            $params = array_merge($requestParams, ['page' => $page]);

            return $this->urlGenerator->generate($crudName, 'list', $params);
        };

        return $view->render($pagerfanta, $routeGenerator, $options);
    }

    public function entityName($crudName)
    {
        return $this->store->getLabelConfig($crudName)->getEntityName();
    }

    public function entityLabel($crudName, $entity)
    {
        return $this->store->getLabelConfig($crudName)->getEntityLabel($entity);
    }

    public function getName()
    {
        return 'crud';
    }
}
