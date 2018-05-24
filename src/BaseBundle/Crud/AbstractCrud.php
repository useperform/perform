<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\Form\Type\CrudType;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\LabelConfig;
use Perform\BaseBundle\Config\ExportConfig;
use Perform\BaseBundle\Util\StringUtil;
use Doctrine\Common\Inflector\Inflector;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractCrud implements CrudInterface
{
    protected $routePrefix;

    public function getFormType()
    {
        return CrudType::class;
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

    public function configureExports(ExportConfig $config)
    {
        $config->setFilename(str_replace(' ', '-', strtolower(Inflector::pluralize(StringUtil::crudClassToEntityName(static::class)))));
    }

    public function configureActions(ActionConfig $config)
    {
        $this->addViewAction($config);
        $this->addEditAction($config);
        $config->add('perform_base_delete');
    }

    protected function addViewAction(ActionConfig $config)
    {
        $config->addLink(function ($entity, $crudUrlGenerator) use ($config) {
            return $crudUrlGenerator->generate($config->getCrudName(), 'view', ['entity' => $entity]);
        },
            'View',
            [
                'isButtonAvailable' => function ($entity, $request) {
                    return $request->getContext() !== 'view';
                },
                'isGranted' => function ($entity, $authChecker) use ($config) {
                    return $authChecker->isGranted('VIEW', $config->getCrudName())
                        && $authChecker->isGranted('VIEW', $entity);
                },
                'buttonStyle' => 'btn-primary',
            ]);
    }

    protected function addEditAction(ActionConfig $config)
    {
        $config->addLink(function ($entity, $crudUrlGenerator) use ($config) {
            return $crudUrlGenerator->generate($config->getCrudName(), 'edit', ['entity' => $entity]);
        },
            'Edit',
            [
                'isGranted' => function ($entity, $authChecker) use ($config) {
                    return $authChecker->isGranted('EDIT', $config->getCrudName())
                        && $authChecker->isGranted('EDIT', $entity);
                },
                'buttonStyle' => 'btn-warning',
            ]);
    }

    public function configureLabels(LabelConfig $config)
    {
        $config->setEntityName(StringUtil::crudClassToEntityName(static::class))
            ->setEntityLabel(function ($entity) {
                return $entity->getId();
            });
    }

    public function getTemplate(Environment $twig, $crudName, $context)
    {
        //try a template in the entity bundle first, e.g.
        //@PerformContact/crud/message/view.html.twig
        $template = StringUtil::templateForCrud(static::class, $context);

        return $twig->getLoader()->exists($template) ? $template : sprintf('@PerformBase/crud/%s.html.twig', $context);
    }

    public static function getEntityClass()
    {
        $entityClass = StringUtil::entityClassForCrud(static::class);
        if (!class_exists($entityClass)) {
            throw new \Exception(sprintf('Unable to guess the entity class to use for %s - tried "%s" but the class does not exist. You should implement %s::getEntityClass().', static::class, $entityClass, static::class));
        }

        return $entityClass;
    }
}
