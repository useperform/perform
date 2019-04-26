<?php

namespace Perform\RichContentBundle\Tests\Block;

use PHPUnit\Framework\TestCase;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Perform\RichContentBundle\BlockType\BlockTypeInterface;
use Perform\RichContentBundle\Exception\BlockTypeNotFoundException;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeRegistryTest extends TestCase
{
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();
    }

    public function testGet()
    {
        $type = $this->createMock(BlockTypeInterface::class);
        $this->registry->add('foo', $type);
        $this->assertSame($type, $this->registry->get('foo'));
    }

    public function testGetUnknown()
    {
        $this->expectException(BlockTypeNotFoundException::class);
        $this->registry->get('foo');
    }

    public function testAll()
    {
        $type1 = $this->createMock(BlockTypeInterface::class);
        $this->registry->add('type1', $type1);
        $type2 = $this->createMock(BlockTypeInterface::class);
        $this->registry->add('type2', $type2);
        $type3 = $this->createMock(BlockTypeInterface::class);
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
        $this->registry->add('foo', $this->createMock(BlockTypeInterface::class));
        $this->assertTrue($this->registry->has('foo'));
        $this->assertFalse($this->registry->has('bar'));
    }
}
