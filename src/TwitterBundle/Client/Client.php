<?php

namespace Perform\TwitterBundle\Client;

use Carbon\Carbon;
use Doctrine\Common\Cache\Cache;
use Perform\TwitterBundle\Factory\FactoryInterface;
use Lyrixx\Twitter\Twitter;
use Psr\Log\LoggerInterface;

/**
 * Client
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Client
{
    protected $factory;
    protected $cache;
    protected $ttl;
    protected $client;
    protected $logger;

    public function __construct(FactoryInterface $factory, Cache $cache, $cacheTimeToLive = 300)
    {
        $this->factory = $factory;
        $this->cache = $cache;
        $this->ttl = $cacheTimeToLive;
    }

    /**
     * @return Twitter
     */
    public function getApiClient()
    {
        if (!$this->client) {
            $this->client = $this->factory->create();
            if ($this->logger) {
                $this->client->setLogger($this->logger);
            }
        }

        return $this->client;
    }

    public function setLogger(LoggerInterface $logger)
    {
        if ($this->client) {
            $this->client->setLogger($logger);
        }

        $this->logger = $logger;
    }

    /**
     * Return a subset of the user timeline suitable for public
     * consumption.
     *
     * @return array
     */
    public function getUserTimeline($screenname, $count = 5)
    {
        $cacheKey = 'perform_twitter.timeline.'.$screenname;
        $tweets = $this->cache->fetch($cacheKey);
        if (is_array($tweets)) {
            if ($this->logger) {
                $this->logger->debug(sprintf('Fetched twitter timeline for "%s" from cache.', $screenname));
            }

            return $tweets;
        }

        $params = [
            'screen_name' => $screenname,
            'count' => $count,
        ];

        $response = $this->getApiClient()->query('GET', 'statuses/user_timeline', $params);
        $tweets = json_decode($response->getBody(), true);

        foreach ($tweets as &$tweet) {
            unset($tweet['entities']);
            unset($tweet['retweeted_status']);
            $sent = new \DateTime($tweet['created_at']);
            $tweet['time_ago'] = Carbon::instance($sent)->diffForHumans();
        }

        $this->cache->save($cacheKey, $tweets, $this->ttl);
        if ($this->logger) {
            $this->logger->debug(sprintf('Saving twitter timeline for "%s" to cache, ttl %s.', $screenname, $this->ttl));
        }

        return $tweets;
    }
}
