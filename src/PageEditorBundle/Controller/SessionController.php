<?php

namespace Perform\PageEditorBundle\Controller;

use Perform\PageEditorBundle\SessionManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SessionController extends Controller
{
    /**
     * @Route("/begin")
     */
    public function beginAction(SessionManager $manager, Request $request)
    {
        $manager->start($request->getSession());
        //configure the entry point, store it in the session?
        return $this->redirect('/');
    }

    /**
     * @Route("/end")
     */
    public function endAction(SessionManager $manager, Request $request)
    {
        $manager->stop($request->getSession());
        //configure the exit point
        return $this->redirect('/admin');
    }
}
