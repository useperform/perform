<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Admin\AdminRegistry;
use Symfony\Component\Form\FormView;
use Pagerfanta\View\TwitterBootstrap4View;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Perform\BaseBundle\Admin\ContextRenderer;

/**
 * CrudExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension extends \Twig_Extension
{
    protected $adminRegistry;
    protected $requestStack;

    public function __construct(ContextRenderer $renderer, CrudUrlGenerator $urlGenerator, RequestStack $requestStack)
    {
        $this->renderer = $renderer;
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
            new \Twig_SimpleFunction('perform_crud_paginator', [$this, 'paginator'], ['is_safe' => ['html']]),
        ];
    }

    public function paginator(Pagerfanta $pagerfanta, $entityClass)
    {
        $view = new TwitterBootstrap4View();
        $options = [
            'proximity' => 3,
        ];
        $requestParams = $this->requestStack->getCurrentRequest()->query->all();

        $routeGenerator = function($page) use ($requestParams, $entityClass) {
            $params = array_merge($requestParams, ['page' => $page]);

            return $this->urlGenerator->generate($entityClass, 'list', $params);
        };

        return $view->render($pagerfanta, $routeGenerator, $options);
    }

    public function getName()
    {
        return 'crud';
    }
}
