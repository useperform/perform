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
        $crudName = $crudRequest->getCrudName();
        $templateVariables = [
            'fields' => $this->store->getFieldConfig($crudName)->getTypes($crudRequest->getContext()),
            'filters' => $this->store->getFilterConfig($crudName)->getFilters(),
            'batchActions' => $this->store->getActionConfig($crudName)->getBatchOptionsForRequest($crudRequest),
            'labelConfig' => $this->store->getLabelConfig($crudName),
            'paginator' => $paginator,
            'orderBy' => $orderBy,
            'crudName' => $crudName,
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_LIST, $event);

        return $event->getTemplateVariables();
    }

    public function viewContext(CrudRequest $crudRequest, $entity)
    {
        $crudName = $crudRequest->getCrudName();
        $templateVariables = [
            'fields' => $this->store->getFieldConfig($crudName)->getTypes($crudRequest->getContext()),
            'labelConfig' => $this->store->getLabelConfig($crudName),
            'entity' => $entity,
            'crudName' => $crudName,
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_VIEW, $event);

        return $event->getTemplateVariables();
    }

    public function createContext(CrudRequest $crudRequest, FormView $formView, $entity)
    {
        $crudName = $crudRequest->getCrudName();
        $templateVariables = [
            'entity' => $entity,
            'form' => $formView,
            'crudName' => $crudName,
            'labelConfig' => $this->store->getLabelConfig($crudName),
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_CREATE, $event);

        return $event->getTemplateVariables();
    }

    public function editContext(CrudRequest $crudRequest, FormView $formView, $entity)
    {
        $crudName = $crudRequest->getCrudName();
        $templateVariables = [
            'entity' => $entity,
            'form' => $formView,
            'crudName' => $crudName,
            'labelConfig' => $this->store->getLabelConfig($crudName),
        ];
        $event = new ContextEvent($crudRequest, $templateVariables);
        $this->dispatcher->dispatch(ContextEvent::CONTEXT_EDIT, $event);

        return $event->getTemplateVariables();
    }
}
