<?php

namespace Perform\TwitterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TweetsController extends Controller
{
    public function timelineAction(Request $request)
    {
        $client = $this->get('admin_twitter.client');
        $screenname = $this->getParameter('admin_twitter.screen_name');
        $count = $request->query->get('limit', 5);

        return new JsonResponse($client->getUserTimeline($screenname, $count));
    }
}
