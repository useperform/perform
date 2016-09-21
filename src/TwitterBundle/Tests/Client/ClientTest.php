<?php

namespace TwitterBundle\Tests\Client;

use Doctrine\Common\Cache\ArrayCache;
use Perform\TwitterBundle\Client\Client;
use Guzzle\Http\Message\Response;

/**
 * ClientTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $cache;
    protected $factory;
    protected $client;

    public function setUp()
    {
        $this->cache = new ArrayCache();
        $this->factory = $this->getMock('Perform\TwitterBundle\Factory\FactoryInterface');
        $this->client = new Client($this->factory, $this->cache);
    }

    protected function expectApiClient()
    {
        $apiClient = $this->getMockBuilder('Lyrixx\Twitter\Twitter')
                   ->disableOriginalConstructor()
                   ->getMock();
        $this->factory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($apiClient));

        return $apiClient;
    }

    public function testApiClientIsReused()
    {
        $this->expectApiClient();
        $client = $this->client->getApiClient();
        $this->assertInstanceOf('Lyrixx\Twitter\Twitter', $client);

        $again = $this->client->getApiClient();
        $this->assertSame($client, $again);
    }

    public function testSetLogger()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $apiClient = $this->expectApiClient();
        $this->factory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($apiClient));

        $this->client->getApiClient();
        $this->client->setLogger($logger);
    }

    public function testSetLoggerBeforeClientCreated()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $apiClient = $this->expectApiClient();
        $apiClient->expects($this->once())
            ->method('setLogger')
            ->with($logger);

        $this->client->setLogger($logger);
        $this->client->getApiClient();
    }

    public function testClientIsNotCreatedTooEarly()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->factory->expects($this->never())
            ->method('create');

        $this->client->setLogger($logger);
    }

    public function testGetUserTimeline()
    {
        $apiClient = $this->expectApiClient();
        $response = new Response(200, null, json_encode([
            [
                "created_at" => "Fri May 20 15:41:16 +0000 2016",
                "id" => 733684023076237312,
                "text" => "hello",
            ],
        ]));
        $apiClient->expects($this->once())
            ->method('query')
            ->with('GET', 'statuses/user_timeline', ['screen_name' => 'test', 'count' => 5])
            ->will($this->returnValue($response));

        $tweets = $this->client->getUserTimeline('test');
        $this->assertSame('hello', $tweets[0]['text']);
        $this->assertSame($tweets, $this->cache->fetch('admin_twitter.timeline.test'));
    }

    public function testCacheIsUsedToFetchTimeline()
    {
        $tweets = [[
            "created_at" => "Fri May 20 15:41:16 +0000 2016",
            "id" => 733684023076237312,
            "text" => "hello",
        ]];
        $this->cache->save('admin_twitter.timeline.test', $tweets);

        $this->assertSame($tweets, $this->client->getUserTimeline('test'));
    }

    public function testCacheWorksForDifferentUsers()
    {
        $tweets1 = [[
            "created_at" => "Fri May 20 15:41:16 +0000 2016",
            "id" => 733684023076237312,
            "text" => "hello from test1",
        ]];
        $this->cache->save('admin_twitter.timeline.test1', $tweets1);
        $tweets2 = [[
            "created_at" => "Fri May 20 25:42:26 +0000 2026",
            "id" => 733684023076237322,
            "text" => "hello from test2",
        ]];
        $this->cache->save('admin_twitter.timeline.test2', $tweets2);

        $this->assertSame($tweets1, $this->client->getUserTimeline('test1'));
        $this->assertSame($tweets2, $this->client->getUserTimeline('test2'));
    }

    public function testCacheIsSavedWithTimeToLive()
    {
        $apiClient = $this->expectApiClient();
        $tweets = [[
            "created_at" => "Fri May 20 15:41:16 +0000 2016",
            "id" => 733684023076237312,
            "text" => "hello",
        ]];
        $response = new Response(200, null, json_encode($tweets));
        $apiClient->expects($this->once())
            ->method('query')
            ->with('GET', 'statuses/user_timeline', ['screen_name' => 'test', 'count' => 5])
            ->will($this->returnValue($response));
        $cache = $this->getMock('Doctrine\Common\Cache\Cache');
        $cache->expects($this->once())
            ->method('save')
            ->with('admin_twitter.timeline.test', $this->callback(function($value) {
                return is_array($value);
            }), 500);
        $client = new Client($this->factory, $cache, 500);

        $client->getUserTimeline('test');
    }
}
