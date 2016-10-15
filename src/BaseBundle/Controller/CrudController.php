<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Perform\BaseBundle\Type\TypeConfig;

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
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $deleteForm = $this->createFormBuilder()->getForm();
        $deleteFormView = $deleteForm->createView();
        $this->setFormTheme($deleteFormView);

        return [
            'fields' => $this->getTypeConfig()->getTypes(TypeConfig::CONTEXT_LIST),
            'routePrefix' => $admin->getRoutePrefix(),
            'entities' => $repo->findAll(),
            'deleteForm' => $deleteFormView,
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

    public function createAction(Request $request)
    {
        $this->initialize($request);
        $builder = $this->createFormBuilder($entity = $this->newEntity());
        $admin = $this->getAdmin();
        $form = $this->createForm($admin->getFormType(), $entity, [
            'typeConfig' => $this->getTypeConfig(),
            'typeRegistry' => $this->get('perform_base.type_registry'),
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
            'typeConfig' => $this->getTypeConfig(),
            'typeRegistry' => $this->get('perform_base.type_registry'),
            'context' => 'edit',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();
            $this->addFlash('success', 'Item updated successfully.');

            return $this->redirect($this->get('perform_base.routing.crud_url')->generate($entity, 'list'));
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);

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
            $this->addFlash('success', 'Item removed successfully.');

            return $this->redirect($this->get('perform_base.routing.crud_url')->generate($entity, 'list'));
        }
    }
}
