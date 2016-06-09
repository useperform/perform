<?php

namespace TwitterBundle\Tests\Client;

use Doctrine\Common\Cache\ArrayCache;
use Admin\TwitterBundle\Client\Client;

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
        $this->factory = $this->getMock('Admin\TwitterBundle\Factory\FactoryInterface');
        $this->client = new Client($this->factory, $this->cache);
    }

    public function testApiClientIsReused()
    {
        $apiClient = $this->getMockBuilder('Lyrixx\Twitter\Twitter')
                   ->disableOriginalConstructor()
                   ->getMock();
        $this->factory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($apiClient));

        $client = $this->client->getApiClient();
        $this->assertInstanceOf('Lyrixx\Twitter\Twitter', $client);

        $again = $this->client->getApiClient();
        $this->assertSame($client, $again);
    }

    public function testSetLogger()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $apiClient = $this->getMockBuilder('Lyrixx\Twitter\Twitter')
                   ->disableOriginalConstructor()
                   ->getMock();
        $apiClient->expects($this->once())
            ->method('setLogger')
            ->with($logger);
        $this->factory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($apiClient));

        $this->client->getApiClient();
        $this->client->setLogger($logger);
    }

    public function testSetLoggerBeforeClientCreated()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $apiClient = $this->getMockBuilder('Lyrixx\Twitter\Twitter')
                   ->disableOriginalConstructor()
                   ->getMock();
        $apiClient->expects($this->once())
            ->method('setLogger')
            ->with($logger);
        $this->factory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($apiClient));

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
}
