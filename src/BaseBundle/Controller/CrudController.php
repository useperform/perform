<?php

namespace Perform\BaseBundle\Controller;

use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Crud\EntityManager;
use Perform\BaseBundle\Crud\TemplatePopulator;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;
use Perform\BaseBundle\Selector\EntitySelector;
use Perform\BaseBundle\Twig\Extension\ActionExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudController extends Controller
{
    protected $twig;
    protected $crudRegistry;
    protected $entitySelector;
    protected $entityResolver;
    protected $templatePopulator;
    protected $entityManager;
    protected $urlGenerator;

    protected $crud;

    public function __construct(Environment $twig, CrudRegistry $crudRegistry, EntitySelector $entitySelector, EntityResolver $entityResolver, TemplatePopulator $templatePopulator, EntityManager $entityManager, CrudUrlGeneratorInterface $urlGenerator)
    {
        $this->twig = $twig;
        $this->crudRegistry = $crudRegistry;
        $this->entitySelector = $entitySelector;
        $this->entityResolver = $entityResolver;
        $this->templatePopulator = $templatePopulator;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    protected function initialize(CrudRequest $crudRequest)
    {
        $this->crud = $this->crudRegistry->get($crudRequest->getCrudName());
        $this->twig->getExtension(ActionExtension::class)
            ->setCrudRequest($crudRequest);
    }

    protected function newEntity()
    {
        $crudClass = get_class($this->crud);
        $class = $this->entityResolver->resolve($crudClass::getEntityClass());

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
        $this->twig->getRuntime(FormRenderer::class)
            ->setTheme($formView, '@PerformBase/form_theme.html.twig');
    }

    public function listAction(Request $request)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_LIST);
        $this->initialize($crudRequest);
        list($paginator, $orderBy) = $this->entitySelector->listContext($crudRequest);

        return $this->templatePopulator->listContext($crudRequest, $paginator, $orderBy);
    }

    public function viewAction(Request $request, $id)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_VIEW);
        $this->initialize($crudRequest);
        $entity = $this->entitySelector->viewContext($crudRequest, $id);
        $this->throwNotFoundIfNull($entity, $id);
        $this->denyAccessUnlessGranted('VIEW', $entity);

        return $this->templatePopulator->viewContext($crudRequest, $entity);
    }

    public function createAction(Request $request)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_CREATE);
        $this->initialize($crudRequest);
        $crudName = $crudRequest->getCrudName();
        $this->denyAccessUnlessGranted('CREATE', $crudName);
        $builder = $this->createFormBuilder($entity = $this->newEntity());
        $form = $this->createForm($this->crud->getFormType(), $entity, [
            'crud_name' => $crudName,
            'context' => $crudRequest->getContext(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->create($crudRequest, $entity);
                $this->addFlash('success', 'Item created successfully.');

                return $this->redirect($this->urlGenerator->generate($crudName, CrudRequest::CONTEXT_LIST));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred.');
            }
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);

        return $this->templatePopulator->editContext($crudRequest, $formView, $entity);
    }

    public function editAction(Request $request, $id)
    {
        $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_EDIT);
        $this->initialize($crudRequest);
        $crudName = $crudRequest->getCrudName();
        $entity = $this->entitySelector->editContext($crudRequest, $id);
        $this->throwNotFoundIfNull($entity, $id);
        $this->denyAccessUnlessGranted('EDIT', $entity);
        $form = $this->createForm($this->crud->getFormType(), $entity, [
            'crud_name' => $crudName,
            'context' => $crudRequest->getContext(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->update($crudRequest, $entity);
                $this->addFlash('success', 'Item updated successfully.');

                return $this->redirect($this->urlGenerator->generate($crudName, CrudRequest::CONTEXT_LIST));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An error occurred.');
            }
        }

        $formView = $form->createView();
        $this->setFormTheme($formView);

        return $this->templatePopulator->editContext($crudRequest, $formView, $entity);
    }
}
