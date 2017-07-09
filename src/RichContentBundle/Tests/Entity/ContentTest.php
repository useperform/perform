<?php

namespace Perform\RichContentBundle\Tests\Entity;

use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentTest extends \PHPUnit_Framework_TestCase
{
    private function block($id)
    {
        $b = new Block();
        $refl = new \ReflectionObject($b);
        $prop = $refl->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($b, $id);

        return $b;
    }

    public function testAddBlock()
    {
        $c = new Content();
        $b1 = $this->block(1);
        $b2 = $this->block(2);

        $c->addBlock($b1);
        $c->addBlock($b2);
        $c->addBlock($b2);
        $c->addBlock($b1);

        $this->assertSame([$b1, $b2], $c->getBlocks()->toArray());
        $this->assertSame([1, 2, 2, 1], $c->getBlockOrder());
    }
}
