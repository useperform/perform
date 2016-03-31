<?php

namespace Admin\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * FileController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function listAction()
    {
        $files = $this->getDoctrine()->getRepository('AdminMediaBundle:File')->findAll();

        return [
            'files' => $files,
        ];
    }

    /**
     * @Route("/upload")
     * @Template
     */
    public function uploadAction()
    {
        return [];
    }
}
