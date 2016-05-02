<?php

namespace Admin\TwitterBundle\Client;

use Carbon\Carbon;
use Doctrine\Common\Cache\Cache;
use Admin\TwitterBundle\Factory\FactoryInterface;

/**
 * Fetcher
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Fetcher
{
    protected $factory;
    protected $cache;

    public function __construct(FactoryInterface $factory, Cache $cache = null)
    {
        $this->factory = $factory;
        $this->cache = $cache;
    }

    public function setLogger($logger)
    {
        $this->factory->getClient()->setLogger($logger);
    }


    /**
     * Return a subset of the user timeline suitable for public
     * consumption.
     *
     * @return array
     */
    public function getUserTimeline($screenname, $count = 5)
    {
        $params = [
            'screen_name' => $screenname,
            'count' => $count,
        ];

        $response = $this->factory->getClient()->query('GET', 'statuses/user_timeline', $params);
        $tweets = json_decode($response->getBody(), true);

        foreach ($tweets as &$tweet) {
            unset($tweet['entities']);
            unset($tweet['retweeted_status']);
            $sent = new \DateTime($tweet['created_at']);
            $tweet['time_ago'] = Carbon::instance($sent)->diffForHumans();
        }

        return $tweets;
    }
}
