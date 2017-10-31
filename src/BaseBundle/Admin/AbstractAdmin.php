<?php

namespace Perform\BaseBundle\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\AdminType;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\LabelConfig;
use Symfony\Component\Templating\EngineInterface;
use Perform\BaseBundle\Util\StringUtil;

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

    public function configureFilters(FilterConfig $config)
    {
    }

    public function configureActions(ActionConfig $config)
    {
        $this->addViewAction($config);
        $this->addEditAction($config);
        $config->add('perform_base_delete');
    }

    protected function addViewAction(ActionConfig $config)
    {
        $config->addLink(function($entity, $crudUrlGenerator) {
            return $crudUrlGenerator->generate($entity, 'view');
        },
            'View',
            [
                'isButtonAvailable' => function($entity, $request) {
                    return $request->getContext() !== 'view';
                },
                'buttonStyle' => 'btn-primary',
            ]);
    }

    protected function addEditAction(ActionConfig $config)
    {
        $config->addLink(function($entity, $crudUrlGenerator) {
            return $crudUrlGenerator->generate($entity, 'edit');
        },
            'Edit',
            [
                'buttonStyle' => 'btn-warning',
            ]);
    }

    public function configureLabels(LabelConfig $config)
    {
        $config->setEntityName(StringUtil::adminClassToEntityName(static::class))
            ->setEntityLabel(function($entity) {
                return $entity->getId();
            });
    }

    public function getTemplate(EngineInterface $templating, $entityName, $context)
    {
        //try a template in the entity bundle first, e.g.
        //PerformContactBundle:Message:view.html.twig
        $template = $entityName.':'.$context.'.html.twig';

        return $templating->exists($template) ? $template : 'PerformBaseBundle:Crud:'.$context.'.html.twig';
    }
}
