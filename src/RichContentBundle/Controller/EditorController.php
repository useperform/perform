<?php

namespace Perform\RichContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     * @Route("/content/get/{id}")
     */
    public function getContentAction(Content $content)
    {
        $serializer = $this->get('perform_rich_content.serializer');
        $blockData = [];
        foreach ($content->getBlocks() as $block) {
            $blockData[$block->getId()] = $serializer->serialize($block);
        }

        return new JsonResponse([
            'blocks' => $blockData,
            'order' => $content->getBlockOrder(),
        ]);
    }

    /**
     * @Route("/content/save/{id}")
     * @Method("POST")
     */
    public function saveContentAction(Request $request, Content $content)
    {
        $body = json_decode($request->getContent(), true);
        $newBlocks = $this->get('perform_rich_content.persister')
                   ->saveFromEditor($content, $body['blocks'], $body['newBlocks'], $body['order']);

        return $this->saveResponse($content, $newBlocks, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/content/save-new")
     * @Method("POST")
     */
    public function saveNewContentAction(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        list($content, $newBlocks) = $this->get('perform_rich_content.persister')
                                   ->createFromEditor($body['newBlocks'], $body['order']);

        return $this->saveResponse($content, $newBlocks, JsonResponse::HTTP_CREATED);
    }

    private function saveResponse(Content $content, array $newBlocks, $status)
    {
        $newIds = [];
        foreach ($newBlocks as $tempId => $block) {
            $newIds[$tempId] = $block->getId();
        }

        return new JsonResponse([
            'id' => $content->getId(),
            'newBlocks' => $newIds,
        ], $status);
    }
}
