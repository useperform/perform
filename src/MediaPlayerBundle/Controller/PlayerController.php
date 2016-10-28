<?php

namespace Perform\MediaPlayerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * PlayerController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlayerController extends Controller
{
    /**
     * @Route()
     * @Template
     */
    public function showAction()
    {
        return [];
    }
}
