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

    public function listAction()
    {
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $deleteForm = $this->createFormBuilder()->getForm();

        return [
            'entities' => $repo->findAll(),
            'deleteForm' => $deleteForm->createView(),
        ];
    }

    public function viewAction($id)
    {
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $entity = $repo->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return [
            'entity' => $entity,
        ];
    }

    public function createAction()
    {
        return [];
    }

    public function editAction()
    {
        return [];
    }

    public function deleteAction(Request $request, $id)
    {
        if ($request->getMethod() !== 'POST') {
            throw new NotFoundHttpException();
        }
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $entity = $repo->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

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
