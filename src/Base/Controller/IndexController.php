<?php

namespace Perform\Base\Controller;

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
        return $this->redirectToRoute('admin_base_dashboard_index');
    }
}
