<?php

namespace Perform\TwitterBundle\Factory;

use Lyrixx\Twitter\Twitter;

/**
 * InMemoryFactory
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class InMemoryFactory implements FactoryInterface
{
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

    public function create()
    {
        return new Twitter(
            $this->consumerKey,
            $this->consumerSecret,
            $this->accessToken,
            $this->accessTokenSecret
        );
    }
}
