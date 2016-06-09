<?php

namespace Admin\TwitterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TweetsController extends Controller
{
    public function timelineAction(Request $request)
    {
        $count = $request->query->get('limit', 5);

        $screenname = $this->getParameter('admin_twitter.screen_name');
        $ttl = $this->getParameter('admin_twitter.cache_ttl');
        $client = $this->get('admin_twitter.client');

        return new JsonResponse($client->getUserTimeline($screenname, $count));
    }
}
