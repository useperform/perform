<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\BaseBundle\Annotation\Ajax;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityNotFoundException;
use Perform\BaseBundle\Action\ActionResponse;

/**
 * ActionController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionController extends Controller
{
    /**
     * @Route("/{action}")
     * @Method("POST")
     * @Ajax
     */
    public function indexAction($action, Request $request)
    {
        try {
            $entityClass = $request->request->get('entityClass');
            $ids = $request->request->get('ids', []);
            $options = [];

            $response = $this->get('perform_base.action_runner')
                      ->run($action, $entityClass, $ids, $options);
        } catch (EntityNotFoundException $e) {
            return [
                'code' => 404,
                'message' => $e->getMessage(),
            ];
        }

        $json = [
            'message' => $response->getMessage(),
            'redirectType' => $response->getRedirect(),
        ];

        if ($response->getRedirect() === ActionResponse::REDIRECT_URL) {
            $json['redirect'] = $response->getUrl();
        }
        if ($response->getRedirect() === ActionResponse::REDIRECT_ENTITY_DEFAULT) {
            $defaultRoute = $this->get('perform_base.routing.crud_url')->getDefaultEntityRoute($entityClass);
            $response->setRedirectRoute($defaultRoute);
        }
        if ($response->getRedirect() === ActionResponse::REDIRECT_ROUTE) {
            $json['redirect'] = $this->generateUrl($response->getRoute(), $response->getRouteParams());
        }
        if (!in_array($response->getRedirect(), [ActionResponse::REDIRECT_NONE, ActionResponse::REDIRECT_URL])) {
            $this->addFlash('success', $response->getMessage());
        }

        return $json;
    }
}
