<?php

namespace Admin\CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Admin\Base\Annotation\Ajax;
use Admin\CmsBundle\Entity\Version;

/**
 * EditorController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Ajax()
     * @Route("/load-version/{id}")
     */
    public function loadVersionAction(Version $version)
    {
        return $version->toArray();
    }
}
