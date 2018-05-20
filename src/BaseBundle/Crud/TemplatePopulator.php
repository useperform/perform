<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\Config\ConfigStoreInterface;
use Pagerfanta\PagerfantaInterface;
use Perform\BaseBundle\Event\ContextEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormView;

/**
 * Gets the required template variables for each crud context.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TemplatePopulator
{
    protected $store;
    protected $crudRegistry;
    protected $dispatcher;

    public function __construct(ConfigStoreInterface $store, CrudRegistry $crudRegistry, EventDispatcherInterface $dispatcher)
    {
        $this->store = $store;
        $this->crudRegistry = $crudRegistry;
        $this->dispatcher = $dispatcher;
    }

    public function listContext(CrudRequest $crudRequest, PagerfantaInterface $paginator, array $orderBy)
    {
        $entityClass = $crudRequest->getEntityClass();
        $templateVariables = [
            'fields' => $this->store->getTypeConfig($entityClass)->getTypes($crudRequest->getContext()),
            'filters' => $this->store->getFilterConfig($entityClass)->getFilters(),
            'batchActions' => $this->store->getActionConfig($entityClass)->getBatchOptionsForRequest($crudRequest),
            'labelConfig' => $this->store->getLabelConfig($entityClass),
            'routePrefix' => $this->crudRegistry->get($entityClass)->getRoutePrefix(),
            'paginator' => $paginator,
            'orderBy' => $orderBy,
            'entityClass' => $entityClass,
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_LIST, $event);

        return $event->getTemplateVariables();
    }

    public function viewContext(CrudRequest $crudRequest, $entity)
    {
        $entityClass = $crudRequest->getEntityClass();
        $templateVariables = [
            'fields' => $this->store->getTypeConfig($entityClass)->getTypes($crudRequest->getContext()),
            'labelConfig' => $this->store->getLabelConfig($entityClass),
            'entity' => $entity,
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_VIEW, $event);

        return $event->getTemplateVariables();
    }

    public function createContext(CrudRequest $crudRequest, FormView $formView, $entity)
    {
        $entityClass = $crudRequest->getEntityClass();
        $templateVariables = [
            'entity' => $entity,
            'form' => $formView,
            'labelConfig' => $this->store->getLabelConfig($entityClass),
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_CREATE, $event);

        return $event->getTemplateVariables();
    }

    public function editContext(CrudRequest $crudRequest, FormView $formView, $entity)
    {
        $entityClass = $crudRequest->getEntityClass();
        $templateVariables = [
            'entity' => $entity,
            'form' => $formView,
            'labelConfig' => $this->store->getLabelConfig($entityClass),
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_EDIT, $event);

        return $event->getTemplateVariables();
    }
}
