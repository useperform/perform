<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * IndexController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class IndexController extends Controller
{
    /**
     * @Route()
     */
    public function indexAction()
    {
        return $this->redirectToRoute('perform_base_dashboard_index');
    }
}
