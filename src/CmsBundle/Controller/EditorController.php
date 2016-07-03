<?php

namespace Admin\CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Admin\Base\Annotation\Ajax;
use Admin\CmsBundle\Entity\Version;
use Symfony\Component\HttpFoundation\Request;

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
    public function loadVersionAction(Request $request, Version $version)
    {
        $sharedSections = $this->get('admin_cms.section_locator')
                        ->findCurrentSections($request->query->get('shared', []));

        return [
            'sections' => $version->toArray(),
            'sharedSections' => $sharedSections,
        ];
    }
}
