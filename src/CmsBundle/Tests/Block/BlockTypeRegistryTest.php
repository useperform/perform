<?php

namespace Perform\CmsBundle\Tests\Block;

use Perform\CmsBundle\Block\BlockTypeRegistry;
use Perform\CmsBundle\Block\BlockTypeInterface;
use Perform\CmsBundle\Exception\BlockTypeNotFoundException;
use Perform\CmsBundle\Entity\Block;

/**
 * BlockTypeRegistryTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $twig;
    protected $registry;

    public function setUp()
    {
        $this->twig = $this->getMock('\Twig_Environment');
        $this->registry = new BlockTypeRegistry($this->twig);
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

    public function testRenderBlock()
    {
        $type = $this->getMock(BlockTypeInterface::class);
        $this->registry->addType('foo', $type);
        $block = new Block();
        $block->setType('foo');
        $type->expects($this->once())
            ->method('render')
            ->with($block)
            ->will($this->returnValue('rendered content'));

        $this->assertSame('rendered content', $this->registry->renderBlock($block));
    }
}
