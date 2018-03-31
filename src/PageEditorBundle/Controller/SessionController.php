<?php

namespace Perform\PageEditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Perform\PageEditorBundle\EventListener\ToolbarListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SessionController extends Controller
{
    /**
     * @Route("/begin")
     */
    public function beginAction(Request $request)
    {
        $request->getSession()->set(ToolbarListener::SESSION_KEY, true);
        //configure the entry point
        return $this->redirect('/');
    }

    /**
     * @Route("/end")
     */
    public function endAction(Request $request)
    {
        $request->getSession()->remove(ToolbarListener::SESSION_KEY);
        //configure the exit point
        return $this->redirect('/admin');
    }
}
