<?php

namespace Perform\BaseBundle\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\AdminType;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Symfony\Component\Templating\EngineInterface;

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
        return $entity->getId();
    }

    public function configureFilters(FilterConfig $config)
    {
    }

    public function configureActions(ActionConfig $config)
    {
        $config->add('perform_base_delete');
    }

    public function getTemplate(EngineInterface $templating, $entityName, $context)
    {
        //try a template in the entity bundle first, e.g.
        //PerformContactBundle:Message:view.html.twig
        $template = $entityName.':'.$context.'.html.twig';

        return $templating->exists($template) ? $template : 'PerformBaseBundle:Crud:'.$context.'.html.twig';
    }
}
