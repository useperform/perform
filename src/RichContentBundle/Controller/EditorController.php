<?php

namespace Perform\RichContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Perform\RichContentBundle\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/content/save/{id}")
     * @Method("POST")
     * @Template
     */
    public function saveContentAction(Request $request, Content $content)
    {
        $body = json_decode($request->getContent(), true);
        $this->get('perform_rich_content.persister')
            ->saveFromEditor($content, $body['blocks'], $body['newBlocks'], $body['order']);

        return new JsonResponse([
            'done' => true,
        ]);
    }
}
