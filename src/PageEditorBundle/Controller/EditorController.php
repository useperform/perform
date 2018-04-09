<?php

namespace Perform\PageEditorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\PageEditorBundle\Entity\Version;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Route("/load/{id}")
     */
    public function loadVersionAction(NormalizerInterface $normalizer, Version $version)
    {
        return new JsonResponse($normalizer->normalize($version, null, ['groups' => ['default']]));
    }

    /**
     * @Route("/save/{id}")
     * @Method("POST")
     */
    public function saveVersionAction(Request $request, Version $version)
    {
    }

    /**
     * @Route("/publish/{id}")
     * @Method("POST")
     */
    public function publishVersionAction(Version $version)
    {
    }
}
