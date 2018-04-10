<?php

namespace Perform\RichContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Perform\RichContentBundle\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Perform\RichContentBundle\Persister\CreateOperation;
use Perform\RichContentBundle\Persister\UpdateOperation;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Route("/content/get/{id}")
     */
    public function getContentAction(NormalizerInterface $normalizer, Content $content)
    {
        return new JsonResponse($normalizer->normalize($content, null, ['groups' => ['default']]));
    }

    /**
     * @Route("/content/save/{id}")
     * @Method("POST")
     */
    public function saveContentAction(Request $request, Content $content)
    {
        $body = json_decode($request->getContent(), true);
        $operation = new UpdateOperation($content, $body['blocks'], $body['newBlocks'], $body['order']);
        $result = $this->get('perform_rich_content.persister')
                ->save($operation);

        return $result->toJsonResponse(JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/content/save-new")
     * @Method("POST")
     */
    public function saveNewContentAction(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $operation = new CreateOperation($body['newBlocks'], $body['order']);
        $result = $this->get('perform_rich_content.persister')
                ->save($operation);

        return $result->toJsonResponse(JsonResponse::HTTP_CREATED);
    }
}
