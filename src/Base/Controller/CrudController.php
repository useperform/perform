<?php

namespace Admin\Base\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        $this->entity = $request->attributes->get('_entity');
    }

    /**
     * @return AdminInterface
     */
    protected function getAdmin()
    {
        return $this->get('admin_base.admin.registry')
            ->getAdmin($this->entity);
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

    public function listAction(Request $request)
    {
        $this->initialize($request);
        $admin = $this->getAdmin();
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $deleteForm = $this->createFormBuilder()->getForm();
        $deleteFormView = $deleteForm->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($deleteFormView, 'AdminBaseBundle::form_theme.html.twig');

        return [
            'fields' => $admin->getListFields(),
            'routePrefix' => $admin->getRoutePrefix(),
            'entities' => $repo->findAll(),
            'deleteForm' => $deleteFormView,
        ];
    }

    public function viewAction(Request $request, $id)
    {
        $this->initialize($request);

        return [
            'fields' => $this->getAdmin()->getViewFields(),
            'entity' => $this->findEntity($id),
        ];
    }

    public function createAction(Request $request)
    {
        $this->initialize($request);
        $builder = $this->createFormBuilder($entity = $this->newEntity());
        $admin = $this->getAdmin();
        $form = $this->createForm($admin->getFormType(), $entity, [
            'admin' => $admin,
            'typeRegistry' => $this->get('admin_base.type_registry'),
            'context' => 'create',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();

            return $this->redirect($this->get('admin_base.routing.crud_url')->generate($entity, 'list'));
        }

        $formView = $form->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, 'AdminBaseBundle::form_theme.html.twig');

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
            'admin' => $admin,
            'typeRegistry' => $this->get('admin_base.type_registry'),
            'context' => 'edit',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();

            return $this->redirect($this->get('admin_base.routing.crud_url')->generate($entity, 'list'));
        }

        $formView = $form->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, 'AdminBaseBundle::form_theme.html.twig');

        return [
            'entity' => $entity,
            'form' => $formView,
        ];
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

            return $this->redirect($this->get('admin_base.routing.crud_url')->generate($entity, 'list'));
        }
    }
}
