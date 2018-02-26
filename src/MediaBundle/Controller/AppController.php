<?php

namespace Perform\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AppController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request)
    {
        return [];
    }
}
