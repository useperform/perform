<?php

namespace Perform\RichContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Route("/version/{id}")
     * @Template
     */
    public function getVersionAction()
    {
        return new JsonResponse([
            'blocks' => [
                '1' => [
                    'type' => 'Text',
                    'value' => 'Test from server',
                ],
                '2' => [
                    'type' => 'Image',
                    'value' => '#',
                ],
            ],
            'order' => [
                1, 2, 1,
            ],
        ]);
    }
}
