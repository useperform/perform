<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Perform\BaseBundle\Type\TypeConfig;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Perform\BaseBundle\Filter\FilterConfig;

/**
 * CrudController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudController extends Controller
{
    protected $entity;

    protected function initialize(Request $request)
    {
        $this->entity = $this->get('perform_base.doctrine.entity_resolver')->resolve($request->attributes->get('_entity'));
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
        return $this->get('perform_base.entity_type_config')
            ->getEntityTypeConfig($this->entity);
    }

    protected function getFilterConfig()
    {
        return $this->get('perform_base.entity_type_config')
            ->getEntityFilterConfig($this->entity);
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
        $this->initialize($request);
        $admin = $this->getAdmin();
        $deleteForm = $this->createFormBuilder()->getForm();
        $deleteFormView = $deleteForm->createView();
        $this->setFormTheme($deleteFormView);
        $selector = $this->get('perform_base.selector.entity');
        list($paginator, $orderBy) = $selector->listContext($request, $this->entity);

        return [
            'fields' => $this->getTypeConfig()->getTypes(TypeConfig::CONTEXT_LIST),
            'filters' => $this->getFilterConfig()->getFilters(),
            'orderBy' => $orderBy,
            'routePrefix' => $admin->getRoutePrefix(),
            'paginator' => $paginator,
            'deleteForm' => $deleteFormView,
            'entityClass' => $this->entity,
        ];
    }

    public function viewAction(Request $request, $id)
    {
        $this->initialize($request);

        return [
            'fields' => $this->getTypeConfig()->getTypes(TypeConfig::CONTEXT_VIEW),
            'entity' => $this->findEntity($id),
        ];
    }

    public function viewDefaultAction(Request $request)
    {
        $this->initialize($request);

        return $this->viewAction($request, $this->findDefaultEntity()->getId());
    }

    public function createAction(Request $request)
    {
        $this->initialize($request);
        $builder = $this->createFormBuilder($entity = $this->newEntity());
        $admin = $this->getAdmin();
        $form = $this->createForm($admin->getFormType(), $entity, [
            'entity' => $this->entity,
            'context' => 'create',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
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
        $this->initialize($request);
        $entity = $this->findEntity($id);
        $admin = $this->getAdmin();
        $form = $this->createForm($admin->getFormType(), $entity, [
            'entity' => $this->entity,
            'context' => 'edit',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
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
        $this->initialize($request);

        return $this->editAction($request, $this->findDefaultEntity()->getId());
    }

    public function deleteAction(Request $request, $id)
    {
        $this->initialize($request);
        if ($request->getMethod() !== 'POST') {
            throw new NotFoundHttpException();
        }
        $entity = $this->findEntity($id);

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->remove($entity);
            $manager->flush();
            $this->addFlash('success', 'Item removed successfully.');

            return $this->redirect($this->get('perform_base.routing.crud_url')->generate($entity, 'list'));
        }
    }
}
