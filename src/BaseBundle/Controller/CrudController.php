<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Admin\AdminRequest;
use Perform\BaseBundle\Twig\Extension\ActionExtension;

/**
 * CrudController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudController extends Controller
{
    protected $entity;

    protected function initialize(AdminRequest $request)
    {
        $this->entity = $this->get('perform_base.doctrine.entity_resolver')->resolve($request->getEntity());
        $this->get('twig')
            ->getExtension(ActionExtension::class)
            ->setAdminRequest($request);
    }

    /**
     * @return AdminInterface
     */
    protected function getAdmin()
    {
        return $this->get('perform_base.admin.registry')
            ->getAdmin($this->entity);
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
            ->renderer->setTheme($formView, 'PerformBaseBundle::form_theme.html.twig');
    }

    public function listAction(Request $request)
    {
        $request = new AdminRequest($request, TypeConfig::CONTEXT_LIST);
        $this->initialize($request);
        $admin = $this->getAdmin();
        $selector = $this->get('perform_base.selector.entity');
        list($paginator, $orderBy) = $selector->listContext($request, $this->entity);

        return [
            'fields' => $this->getTypeConfig()->getTypes($request->getContext()),
            'filters' => $this->getFilterConfig()->getFilters(),
            'actions' => $this->getActionConfig()->forRequest($request),
            'orderBy' => $orderBy,
            'routePrefix' => $admin->getRoutePrefix(),
            'paginator' => $paginator,
            'entityClass' => $this->entity,
        ];
    }

    public function viewAction(Request $request, $id)
    {
        $request = new AdminRequest($request, TypeConfig::CONTEXT_VIEW);
        $this->initialize($request);

        return [
            'fields' => $this->getTypeConfig()->getTypes($request->getContext()),
            'entity' => $this->findEntity($id),
        ];
    }

    public function viewDefaultAction(Request $request)
    {
        $this->initialize(new AdminRequest($request, TypeConfig::CONTEXT_VIEW));

        return $this->viewAction($request, $this->findDefaultEntity()->getId());
    }

    public function createAction(Request $request)
    {
        $request = new AdminRequest($request, TypeConfig::CONTEXT_CREATE);
        $this->initialize($request);
        $builder = $this->createFormBuilder($entity = $this->newEntity());
        $admin = $this->getAdmin();
        $form = $this->createForm($admin->getFormType(), $entity, [
            'entity' => $this->entity,
            'context' => $request->getContext(),
        ]);

        $form->handleRequest($request->getRequest());

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($entity);
            $manager->flush();
            $this->addFlash('success', 'Item created successfully.');

            return $this->redirect($this->get('perform_base.routing.crud_url')->generate($entity, 'list'));
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);

        return [
            'entity' => $entity,
            'form' => $formView,
        ];
    }

    public function editAction(Request $request, $id)
    {
        $request = new AdminRequest($request, TypeConfig::CONTEXT_EDIT);
        $this->initialize($request);
        $entity = $this->findEntity($id);
        $admin = $this->getAdmin();
        $form = $this->createForm($admin->getFormType(), $entity, [
            'entity' => $this->entity,
            'context' => $request->getContext(),
        ]);

        $form->handleRequest($request->getRequest());

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($entity);
            $manager->flush();
            $this->addFlash('success', 'Item updated successfully.');

            $urlGenerator = $this->get('perform_base.routing.crud_url');
            $url = $urlGenerator->routeExists($entity, 'viewDefault') ?
                 $urlGenerator->generate($entity, 'viewDefault') :
                 $urlGenerator->generate($entity, 'list');

            return $this->redirect($url);
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);

        return [
            'entity' => $entity,
            'form' => $formView,
        ];
    }

    public function editDefaultAction(Request $request)
    {
        $this->initialize(new AdminRequest($request, TypeConfig::CONTEXT_EDIT));

        return $this->editAction($request, $this->findDefaultEntity()->getId());
    }
}
