<?php

namespace Admin\CmsBundle\Tests\Block;

use Admin\CmsBundle\Block\BlockTypeRegistry;
use Admin\CmsBundle\Block\BlockTypeInterface;
use Admin\CmsBundle\Exception\BlockTypeNotFoundException;

/**
 * BlockTypeRegistryTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();
    }

    public function testGetType()
    {
        $type = $this->getMock(BlockTypeInterface::class);
        $this->registry->addType('foo', $type);
        $this->assertSame($type, $this->registry->getType('foo'));
    }

    public function testGetUnknownType()
    {
        $this->setExpectedException(BlockTypeNotFoundException::class);
        $this->registry->getType('foo');
    }

    public function testGetTypes()
    {
        $type = $this->getMock(BlockTypeInterface::class);
        $this->registry->addType('foo', $type);
        $this->registry->addType('bar', $type);
        $expected = [
            'foo' => $type,
            'bar' => $type,
        ];
        $this->assertSame($expected, $this->registry->getTypes());
    }
}
