<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Exception\BlockTypeNotFoundException;

/**
 * Stores all the available content block types.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeRegistry
{
    protected $types = [];

    public function add($name, BlockTypeInterface $type)
    {
        $this->types[$name] = $type;
    }

    public function get($name)
    {
        if (!isset($this->types[$name])) {
            throw new BlockTypeNotFoundException(sprintf('Block type "%s" not found.', $name));
        }

        return $this->types[$name];
    }

    public function all()
    {
        return $this->types;
    }
}
