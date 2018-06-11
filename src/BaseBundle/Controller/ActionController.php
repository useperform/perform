<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityNotFoundException;
use Perform\BaseBundle\Action\ActionResponse;
use Perform\BaseBundle\Action\ActionFailedException;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionController extends Controller
{
    /**
     * @Route("/{action}")
     * @Method("POST")
     */
    public function indexAction($action, Request $request)
    {
        try {
            $crudName = $request->request->get('crudName');
            $ids = $request->request->get('ids', []);
            $options = $request->request->get('options', []);

            $response = $this->get('perform_base.action_runner')
                      ->run($crudName, $action, $ids, $options);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        } catch (ActionFailedException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $json = [
            'message' => $response->getMessage(),
            'redirectType' => $response->getRedirect(),
        ];

        if ($response->getRedirect() === ActionResponse::REDIRECT_URL) {
            $json['redirect'] = $response->getUrl();
        }
        if ($response->getRedirect() === ActionResponse::REDIRECT_LIST_CONTEXT) {
            $json['redirect'] = $this->get('perform_base.routing.crud_generator')->generate($crudName, CrudRequest::CONTEXT_LIST, $response->getRouteParams());
        }
        if ($response->getRedirect() === ActionResponse::REDIRECT_ROUTE) {
            $json['redirect'] = $this->generateUrl($response->getRoute(), $response->getRouteParams());
        }
        if (!in_array($response->getRedirect(), [ActionResponse::REDIRECT_NONE, ActionResponse::REDIRECT_URL])) {
            $this->addFlash('success', $response->getMessage());
        }

        return new JsonResponse($json);
    }
}
