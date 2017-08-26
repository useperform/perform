<?php

namespace Perform\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Controller\CrudController;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserController extends CrudController
{
    public function viewAction(Request $request, $id)
    {
        $vars = parent::viewAction($request, $id);

        $vars['tokens'] = $this->getDoctrine()->getManager()
                        ->getRepository('PerformUserBundle:ResetToken')
                        ->findBy(['user' => $vars['entity']]);

        return $vars;
    }
}
