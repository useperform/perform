<?php

namespace Admin\Base\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * DashboardController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DashboardController extends Controller
{
    /**
     * @Route("/dashboard")
     * @Template
     */
    public function indexAction()
    {
        return [];
    }
}
