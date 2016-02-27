<?php

namespace Admin\Base\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        return [
            'entities' => $repo->findAll(),
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

    public function deleteAction()
    {
        return [];
    }
}
