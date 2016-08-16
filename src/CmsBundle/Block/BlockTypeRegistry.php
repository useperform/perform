<?php

namespace Admin\CmsBundle\Block;

use Admin\CmsBundle\Exception\BlockTypeNotFoundException;
use Admin\CmsBundle\Entity\Block;

/**
 * BlockTypeRegistry
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeRegistry
{
    protected $types = [];

    /**
     * @param string $name
     * @param BlockTypeInterface $type
     */
    public function addType($name, BlockTypeInterface $type)
    {
        $this->types[$name] = $type;
    }

    /**
     * @return BlockTypeInterface
     */
    public function getType($name)
    {
        if (!isset($this->types[$name])) {
            throw new BlockTypeNotFoundException(sprintf('Block type "%s" not found.', $name));
        }

        return $this->types[$name];
    }

    /**
     * @param Block
     * @return string
     */
    public function renderBlock(Block $block)
    {
        return $this->getType($block->getType())->render($block);
    }

    public function getTypes()
    {
        return $this->types;
    }
}
