<?php

namespace Perform\CmsBundle\Block;

use Perform\CmsBundle\Exception\BlockTypeNotFoundException;
use Perform\CmsBundle\Entity\Block;

/**
 * BlockTypeRegistry
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeRegistry
{
    protected $twig;
    protected $types = [];

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

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

    /**
     * @return Twig_TemplateInterface
     */
    public function getEditorTemplate($name)
    {
        return $this->twig->loadTemplate($this->getType($name)->getEditorTemplate());
    }

    public function getTypes()
    {
        return $this->types;
    }
}
