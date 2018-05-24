<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Twig\Extension\ActionExtension;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudController extends Controller
{
    protected $crud;

    protected function initialize(CrudRequest $crudRequest)
    {
        $this->crud = $this->get('perform_base.crud.registry')->get($crudRequest->getCrudName());
        $this->get('twig')
            ->getExtension(ActionExtension::class)
            ->setCrudRequest($crudRequest);
    }

    protected function newEntity()
    {
        $crudClass = get_class($this->crud);
        $class = $crudClass::getEntityClass();

        return new $class();
    }

    protected function throwNotFoundIfNull($entity, $identifier)
    {
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('Entity with identifier "%s" was not found.', $identifier));
        }
    }

    private function setFormTheme($formView)
    {
        $this->get('twig')
            ->getExtension(FormExtension::class)
            ->renderer->setTheme($formView, '@PerformBase/form_theme.html.twig');
    }

    public function listAction(Request $request)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_LIST);
        $this->initialize($crudRequest);
        list($paginator, $orderBy) = $this->get('perform_base.selector.entity')->listContext($crudRequest);
        $populator = $this->get('perform_base.template_populator');

        return $populator->listContext($crudRequest, $paginator, $orderBy);
    }

    public function viewAction(Request $request, $id)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_VIEW);
        $this->initialize($crudRequest);
        $entity = $this->get('perform_base.selector.entity')->viewContext($crudRequest, $id);
        $this->throwNotFoundIfNull($entity, $id);
        $this->denyAccessUnlessGranted('VIEW', $entity);
        $populator = $this->get('perform_base.template_populator');

        return $populator->viewContext($crudRequest, $entity);
    }

    public function createAction(Request $request)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_CREATE);
        $this->initialize($crudRequest);
        $crudName = $crudRequest->getCrudName();
        $builder = $this->createFormBuilder($entity = $this->newEntity());
        $form = $this->createForm($this->crud->getFormType(), $entity, [
            'crud_name' => $crudName,
            'context' => $crudRequest->getContext(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get('perform_base.entity_manager')->create($crudRequest, $entity);
                $this->addFlash('success', 'Item created successfully.');

                return $this->redirect($this->get('perform_base.routing.crud_url')->generateDefaultEntityRoute($crudName));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred.');
            }
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);
        $populator = $this->get('perform_base.template_populator');

        return $populator->editContext($crudRequest, $formView, $entity);
    }

    public function editAction(Request $request, $id)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_EDIT);
        $this->initialize($crudRequest);
        $crudName = $crudRequest->getCrudName();
        $entity = $this->get('perform_base.selector.entity')->editContext($crudRequest, $id);
        $this->throwNotFoundIfNull($entity, $id);
        $this->denyAccessUnlessGranted('EDIT', $entity);
        $form = $this->createForm($this->crud->getFormType(), $entity, [
            'crud_name' => $crudName,
            'context' => $crudRequest->getContext(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get('perform_base.entity_manager')->update($crudRequest, $entity);
                $this->addFlash('success', 'Item updated successfully.');

                return $this->redirect($this->get('perform_base.routing.crud_url')->generateDefaultEntityRoute($crudName));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred.');
            }
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);
        $populator = $this->get('perform_base.template_populator');

        return $populator->editContext($crudRequest, $formView, $entity);
    }
}
