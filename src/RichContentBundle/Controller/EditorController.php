<?php

namespace Perform\RichContentBundle\Controller;

use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Persister\OperationInterface;
use Perform\RichContentBundle\Persister\Persister;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Route("/content/get/{id}")
     */
    public function getContentAction(Serializer $serializer, Content $content)
    {
        return new JsonResponse($serializer->normalize($content, null, ['groups' => ['default']]));
    }

    /**
     * @Route("/content/save", methods={"POST"})
     */
    public function saveContentAction(Serializer $serializer, Persister $persister, Request $request)
    {
        $operation = $serializer->deserialize($request->getContent(), OperationInterface::class, 'json');
        $result = $persister->save($operation);

        return new JsonResponse($serializer->normalize($result));
        // JsonResponse::HTTP_CREATED;
    }
}
