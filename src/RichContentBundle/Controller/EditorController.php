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
     * @Route("/content/{id}")
     * @Template
     */
    public function getContentAction()
    {
        $blocks = $this->getDoctrine()
                ->getRepository('PerformRichContentBundle:Block')
                ->findAll();

        $blockData = [];
        foreach ($blocks as $block) {
            $blockData[$block->getId()] = $block->toArray();
        }
        $order = [];
        for ($i = 0; $i < 5; $i++) {
            $order[] = $blocks[array_rand($blocks)]->getId();
        }

        return new JsonResponse([
            'blocks' => $blockData,
            'order' => $order,
        ]);
    }
}
