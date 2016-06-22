<?php

namespace Admin\TwitterBundle\Tests\Factory;

use Admin\TwitterBundle\Factory\InMemoryFactory;

/**
 * InMemoryFactoryTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class InMemoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->factory = new InMemoryFactory('consumer', 'consumer_secret', 'token', 'token_secret');
    }

    public function testCreate()
    {
        $client = $this->factory->create();
        $this->assertInstanceOf('Lyrixx\Twitter\Twitter', $client);

        $this->assertNotSame($client, $this->factory->create());
    }
}
