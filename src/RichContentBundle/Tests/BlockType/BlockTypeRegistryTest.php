<?php

namespace Perform\RichContentBundle\Tests\Block;

use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Perform\RichContentBundle\BlockType\BlockTypeInterface;
use Perform\RichContentBundle\Exception\BlockTypeNotFoundException;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();
    }

    public function testGet()
    {
        $type = $this->getMock(BlockTypeInterface::class);
        $this->registry->add('foo', $type);
        $this->assertSame($type, $this->registry->get('foo'));
    }

    public function testGetUnknown()
    {
        $this->setExpectedException(BlockTypeNotFoundException::class);
        $this->registry->get('foo');
    }

    public function testAll()
    {
        $type1 = $this->getMock(BlockTypeInterface::class);
        $this->registry->add('type1', $type1);
        $type2 = $this->getMock(BlockTypeInterface::class);
        $this->registry->add('type2', $type2);
        $type3 = $this->getMock(BlockTypeInterface::class);
        $this->registry->add('type3', $type3);

        $expected = [
            'type1' => $type1,
            'type2' => $type2,
            'type3' => $type3,
        ];
        $this->assertSame($expected, $this->registry->all());
    }

    public function testHas()
    {
        $this->registry->add('foo', $this->getMock(BlockTypeInterface::class));
        $this->assertTrue($this->registry->has('foo'));
        $this->assertFalse($this->registry->has('bar'));
    }
}
