<?php

namespace MediaBundle\Tests\MediaType;

use Perform\MediaBundle\Exception\MediaTypeException;
use Perform\MediaBundle\MediaType\MediaTypeInterface;
use Perform\MediaBundle\MediaType\MediaTypeRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaTypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $locator;
    protected $registry;

    public function setUp()
    {
        $this->locator = $this->getMockBuilder(ServiceLocator::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->registry = new MediaTypeRegistry($this->locator);
    }

    public function testGet()
    {
        $type = $this->getMock(MediaTypeInterface::class);
        $this->locator->expects($this->any())
            ->method('has')
            ->with('test')
            ->will($this->returnValue(true));
        $this->locator->expects($this->any())
            ->method('get')
            ->with('test')
            ->will($this->returnValue($type));
        $this->assertSame($type, $this->registry->get('test'));
    }

    public function testGetUnknown()
    {
        $this->setExpectedException(MediaTypeException::class);
        $this->registry->get('foo');
    }
}
