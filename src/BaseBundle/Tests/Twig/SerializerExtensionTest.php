<?php

namespace Perform\BaseBundle\Tests\Twig;

use Perform\BaseBundle\Twig\Extension\SerializerExtension;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $serializer;
    protected $extension;

    public function setUp()
    {
        $this->serializer = $this->getMock(SerializerInterface::class);
        $this->extension = new SerializerExtension($this->serializer);
    }

    public function testSerialize()
    {
        $obj = new \stdClass();
        $this->serializer->expects($this->any())
            ->method('serialize')
            ->with($obj)
            ->will($this->returnValue('[data]'));

        $this->assertSame('[data]', $this->extension->serialize($obj, 'json'));
    }
}
