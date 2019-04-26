<?php

namespace Perform\RichContentBundle\Tests\Renderer;

use PHPUnit\Framework\TestCase;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Perform\RichContentBundle\Renderer\Renderer;
use Perform\RichContentBundle\BlockType\BlockTypeInterface;
use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RendererTest extends TestCase
{
    protected $registry;
    protected $renderer;

    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();
        $this->renderer = new Renderer($this->registry);
    }

    private function addType($name)
    {
        $type = $this->createMock(BlockTypeInterface::class);
        $this->registry->add($name, $type);

        return $type;
    }

    private function block($type)
    {
        $b = new Block();
        $refl = new \ReflectionObject($b);
        $prop = $refl->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($b, uniqid());
        $b->setType($type);

        return $b;
    }

    public function testRender()
    {
        $c = new Content();
        $b1 = $this->block('test');
        $b2 = $this->block('test2');
        $c->addBlock($b1);
        $c->addBlock($b2);
        $c->addBlock($b1);

        $test = $this->addType('test');
        $test->expects($this->any())
            ->method('render')
            ->with($b1)
            ->will($this->returnValue('html_test '));
        $test2 = $this->addType('test2');
        $test2->expects($this->any())
            ->method('render')
            ->with($b2)
            ->will($this->returnValue('html_test2 '));

        $this->assertSame('html_test html_test2 html_test ', $this->renderer->render($c));
    }
}
