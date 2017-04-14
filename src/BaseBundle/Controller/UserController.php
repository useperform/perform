<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * UserController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserController extends CrudController
{
    public function viewAction(Request $request, $id)
    {
        $vars = parent::viewAction($request, $id);

        $vars['tokens'] = $this->getDoctrine()->getManager()
                        ->getRepository('PerformBaseBundle:ResetToken')
                        ->findBy(['user' => $vars['entity']]);

        return $vars;
    }
}
