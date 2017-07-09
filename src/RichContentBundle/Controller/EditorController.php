<?php

namespace Perform\RichContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Perform\RichContentBundle\Entity\Content;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Route("/content/{id}")
     * @Template
     */
    public function getContentAction(Content $content)
    {
        $blockData = [];
        foreach ($content->getBlocks() as $block) {
            $blockData[$block->getId()] = $block->toArray();
        }

        return new JsonResponse([
            'blocks' => $blockData,
            'order' => $content->getBlockOrder(),
        ]);
    }
}
