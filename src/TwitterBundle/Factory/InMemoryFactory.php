<?php

namespace Admin\TwitterBundle\Factory;

use Lyrixx\Twitter\Twitter;

/**
 * InMemoryFactory
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class InMemoryFactory implements FactoryInterface
{
    protected $client;
    protected $consumerKey;
    protected $consumerSecret;
    protected $accessToken;
    protected $accessTokenSecret;

    public function __construct($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->accessToken = $accessToken;
        $this->accessTokenSecret = $accessTokenSecret;
    }

    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Twitter(
                $this->consumerKey,
                $this->consumerSecret,
                $this->accessToken,
                $this->accessTokenSecret
            );
        }

        return $this->client;
    }
}
