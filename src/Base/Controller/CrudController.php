<?php

namespace Admin\Base\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Admin\Base\Entity\User;

/**
 * CrudController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class CrudController extends Controller
{
    public static function getCrudActions()
    {
        return [
            '/' => 'list',
            '/view/{id}' => 'view',
            '/create' => 'create',
            '/edit/{id}' => 'edit',
            '/delete/{id}' => 'delete',
        ];
    }

    /**
     * @return AdminInterface
     */
    protected function getAdmin()
    {
        return $this->get('admin_base.admin.registry')
            ->getAdmin($this->entity);
    }

    protected function getEntity($id)
    {
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $entity = $repo->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    public function listAction()
    {
        $admin = $this->getAdmin();
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $deleteForm = $this->createFormBuilder()->getForm();
        $deleteFormView = $deleteForm->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($deleteFormView, 'bootstrap_3_layout.html.twig');

        return [
            'fields' => $admin->getListFields(),
            'entities' => $repo->findAll(),
            'deleteForm' => $deleteFormView,
        ];
    }

    public function viewAction($id)
    {
        return [
            'fields' => $this->getAdmin()->getViewFields(),
            'entity' => $this->getEntity($id),
        ];
    }

    public function createAction(Request $request)
    {
        $builder = $this->createFormBuilder($entity = new User());
        $this->getAdmin()->buildCreateForm($builder, $entity);
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();

            return $this->redirect('/admin/users');
        }

        $formView = $form->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, 'bootstrap_3_layout.html.twig');

        return [
            'form' => $formView,
        ];
    }

    public function editAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createFormBuilder($entity)
              ->add('forename')
              ->add('surname')
              ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();

            return $this->redirect('/admin/users');
        }

        $formView = $form->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, 'bootstrap_3_layout.html.twig');

        return [
            'form' => $formView,
        ];
    }

    public function deleteAction(Request $request, $id)
    {
        if ($request->getMethod() !== 'POST') {
            throw new NotFoundHttpException();
        }
        $entity = $this->getEntity($id);

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->remove($entity);
            $manager->flush();

            return new RedirectResponse('/admin/users');
        }
    }
}
