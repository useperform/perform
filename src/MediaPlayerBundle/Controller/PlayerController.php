<?php

namespace Perform\MediaPlayerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Perform\MediaPlayerBundle\Entity\Playlist;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * PlayerController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlayerController extends Controller
{
    /**
     * @Route()
     * @Template
     */
    public function showAction()
    {
        return [];
    }

    /**
     * @Route("/playlist/{id}")
     */
    public function playlistAction(Playlist $playlist)
    {
        $data = [
            'playlist' => [
                'id' => $playlist->getId(),
                'title' => $playlist->getTitle(),
            ],
            'items' => []
        ];
        foreach ($playlist->getItems() as $item) {
            $data['items'][] = [
                'url' => $item->getFile()->getFileName(),
            ];
        }

        return new JsonResponse($data);
    }
}
