<?php

namespace Perform\PageEditorBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\PageEditorBundle\Entity\Version;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Perform\PageEditorBundle\Repository\VersionRepository;
use Perform\PageEditorBundle\Persister\Persister;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EditorController extends Controller
{
    /**
     * @Route("/load/{id}")
     */
    public function loadVersionAction(VersionRepository $repo, NormalizerInterface $normalizer, Version $version)
    {
        $availableVersions = $repo->findRelated($version);

        return new JsonResponse([
            'version' => $normalizer->normalize($version, null, ['groups' => ['default']]),
            'availableVersions' => $normalizer->normalize($availableVersions, null, ['groups' => ['summary']]),
        ]);
    }

    /**
     * @Route("/save", methods={"POST"})
     */
    public function saveVersionAction(SerializerInterface $serializer, Persister $persister, Request $request)
    {
        $update = $serializer->deserialize($request->getContent(), 'Perform\PageEditorBundle\Persister\VersionUpdate', 'json');
        $results = $persister->save($update);

        return new JsonResponse([
            'updates' => $results,
        ]);
    }

    /**
     * @Route("/publish/{id}", methods={"POST"})
     */
    public function publishVersionAction(VersionRepository $repo, Version $version)
    {
        $repo->markPublished($version);

        return new JsonResponse([]);
    }
}
