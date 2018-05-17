<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Twig\Extension\ActionExtension;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudController extends Controller
{
    protected $entity;

    protected function initialize(CrudRequest $request)
    {
        $this->entity = $this->get('perform_base.doctrine.entity_resolver')->resolve($request->getEntity());
        $this->get('twig')
            ->getExtension(ActionExtension::class)
            ->setCrudRequest($request);
    }

    /**
     * @return CrudInterface
     */
    protected function getCrud()
    {
        return $this->get('perform_base.crud.registry')
            ->getCrud($this->entity);
    }

    protected function getTypeConfig()
    {
        return $this->get('perform_base.config_store')
            ->getTypeConfig($this->entity);
    }

    protected function getFilterConfig()
    {
        return $this->get('perform_base.config_store')
            ->getFilterConfig($this->entity);
    }

    protected function getActionConfig()
    {
        return $this->get('perform_base.config_store')
            ->getActionConfig($this->entity);
    }

    protected function getLabelConfig()
    {
        return $this->get('perform_base.config_store')
            ->getLabelConfig($this->entity);
    }

    protected function newEntity()
    {
        $className = $this->getDoctrine()
                   ->getManager()
                   ->getClassMetadata($this->entity)
                   ->name;

        return new $className();
    }

    protected function findEntity($id)
    {
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $entity = $repo->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    protected function findDefaultEntity()
    {
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $result = $repo->findBy([], [], 1);
        if (!isset($result[0])) {
            throw new NotFoundHttpException();
        }

        return $result[0];
    }

    private function setFormTheme($formView)
    {
        $this->get('twig')
            ->getExtension(FormExtension::class)
            ->renderer->setTheme($formView, '@PerformBase/form_theme.html.twig');
    }

    public function listAction(Request $request)
    {
        $request = new CrudRequest($request, TypeConfig::CONTEXT_LIST);
        $this->initialize($request);
        $crud = $this->getCrud();
        $selector = $this->get('perform_base.selector.entity');
        list($paginator, $orderBy) = $selector->listContext($request, $this->entity);

        return [
            'fields' => $this->getTypeConfig()->getTypes($request->getContext()),
            'filters' => $this->getFilterConfig()->getFilters(),
            'batchActions' => $this->getActionConfig()->getBatchOptionsForRequest($request),
            'labelConfig' => $this->getLabelConfig(),
            'orderBy' => $orderBy,
            'routePrefix' => $crud->getRoutePrefix(),
            'paginator' => $paginator,
            'entityClass' => $this->entity,
        ];
    }

    public function viewAction(Request $request, $id)
    {
        $request = new CrudRequest($request, TypeConfig::CONTEXT_VIEW);
        $this->initialize($request);
        $entity = $this->findEntity($id);
        $this->denyAccessUnlessGranted('VIEW', $entity);

        return [
            'fields' => $this->getTypeConfig()->getTypes($request->getContext()),
            'entity' => $entity,
            'labelConfig' => $this->getLabelConfig(),
        ];
    }

    public function viewDefaultAction(Request $request)
    {
        $this->initialize(new CrudRequest($request, TypeConfig::CONTEXT_VIEW));

        return $this->viewAction($request, $this->findDefaultEntity()->getId());
    }

    public function createAction(Request $request)
    {
        $request = new CrudRequest($request, TypeConfig::CONTEXT_CREATE);
        $this->initialize($request);
        $builder = $this->createFormBuilder($entity = $this->newEntity());
        $crud = $this->getCrud();
        $form = $this->createForm($crud->getFormType(), $entity, [
            'entity' => $this->entity,
            'context' => $request->getContext(),
        ]);

        $form->handleRequest($request->getRequest());

        if ($form->isValid()) {
            try {
                $this->get('perform_base.entity_manager')->create($entity);
                $this->addFlash('success', 'Item created successfully.');

                return $this->redirect($this->get('perform_base.routing.crud_url')->generateDefaultEntityRoute($entity));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred.');
            }
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);

        return [
            'entity' => $entity,
            'form' => $formView,
            'labelConfig' => $this->getLabelConfig(),
        ];
    }

    public function editAction(Request $request, $id)
    {
        $request = new CrudRequest($request, TypeConfig::CONTEXT_EDIT);
        $this->initialize($request);
        $entity = $this->findEntity($id);
        $this->denyAccessUnlessGranted('EDIT', $entity);
        $crud = $this->getCrud();
        $form = $this->createForm($crud->getFormType(), $entity, [
            'entity' => $this->entity,
            'context' => $request->getContext(),
        ]);

        $form->handleRequest($request->getRequest());

        if ($form->isValid()) {
            try {
                $this->get('perform_base.entity_manager')->update($entity);
                $this->addFlash('success', 'Item updated successfully.');

                return $this->redirect($this->get('perform_base.routing.crud_url')->generateDefaultEntityRoute($entity));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred.');
            }
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);

        return [
            'entity' => $entity,
            'form' => $formView,
            'labelConfig' => $this->getLabelConfig(),
        ];
    }

    public function editDefaultAction(Request $request)
    {
        $this->initialize(new CrudRequest($request, TypeConfig::CONTEXT_EDIT));

        return $this->editAction($request, $this->findDefaultEntity()->getId());
    }
}
