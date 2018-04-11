<?php

namespace Perform\PageEditorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\PageEditorBundle\Entity\Version;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Perform\PageEditorBundle\Repository\VersionRepository;
use Perform\PageEditorBundle\Persister\Persister;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Route("/load/{id}")
     */
    public function loadVersionAction(VersionRepository $repo, Serializer $serializer, Version $version)
    {
        $availableVersions = $repo->findRelated($version);

        return new JsonResponse([
            'version' => $serializer->normalize($version, null, ['groups' => ['default']]),
            'availableVersions' => $serializer->normalize($availableVersions, null, ['groups' => ['summary']]),
        ]);
    }

    /**
     * @Route("/save")
     * @Method("POST")
     */
    public function saveVersionAction(Serializer $serializer, Persister $persister, Request $request)
    {
        $update = $serializer->deserialize($request->getContent(), 'Perform\PageEditorBundle\Persister\VersionUpdate', 'json');
        $results = $persister->save($update);

        return new JsonResponse([
            'updates' => $results,
        ]);
    }

    /**
     * @Route("/publish/{id}")
     * @Method("POST")
     */
    public function publishVersionAction(VersionRepository $repo, Version $version)
    {
        $repo->markPublished($version);

        return new JsonResponse([]);
    }
}
