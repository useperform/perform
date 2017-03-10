<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\BaseBundle\Annotation\Ajax;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        $response = $this->get('perform_base.action_runner')
                  ->run($action, $request->request->get('ids', []), $request->request->all());

        $json = [
            'message' => $response->getMessage(),
        ];

        if ($response->getRoute()) {
            $json['redirect'] = $this->generateUrl($response->getRoute(), $response->getRouteParams());
            $this->addFlash('success', $response->getMessage());

        }

        return $json;
    }
}
