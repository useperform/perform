<?php

namespace Perform\TwitterBundle\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Perform\TwitterBundle\Factory\InMemoryFactory;

/**
 * InMemoryFactoryTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class InMemoryFactoryTest extends TestCase
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
