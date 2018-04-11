<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Entity\Block;

/**
 * BlockTypeInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface BlockTypeInterface
{
    /**
     * Transform a block entity of this type into HTML content.
     *
     * @param Block
     *
     * @return string
     */
    public function render(Block $block);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * Get the default value to be passed to a new instance of this block type.
     *
     * @return array
     */
    public function getDefaults();

    /**
     * Get an array of information to pass to the editor vue component for this block.
     *
     * This information is not stored with the block in the database and computed per-request.
     *
     * For example, an image block may generate a URL from a media id stored with the block.
     * The image editor component can then use this URL to display the image.
     * The base URL of images may change, but blocks in the database will not need to be updated.
     */
    public function getComponentInfo(Block $block);
}
