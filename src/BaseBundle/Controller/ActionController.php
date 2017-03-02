<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\BaseBundle\Annotation\Ajax;
use Symfony\Component\HttpFoundation\Request;

/**
 * ActionController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionController extends Controller
{
    /**
     * @Route
     * @Ajax
     */
    public function indexAction(Request $request)
    {
        $this->addFlash('success', 'Action succeeded.');

        return [
            'redirect' => $this->generateUrl('perform_contact_message_list'),
        ];
    }
}
