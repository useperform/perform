<?php

namespace Perform\RichContentBundle;

use Perform\RichContentBundle\Entity\Block;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;

/**
 * Transforms blocks into an array representation to send to the frontend.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Serializer
{
    protected $registry;

    public function __construct(BlockTypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function serialize(Block $block)
    {
        return [
            'id' => $block->getId(),
            'type' => $type = $block->getType(),
            'value' => $block->getValue(),
            'component_info' => (object) $this->registry->get($type)->getComponentInfo($block),
        ];
    }
}
