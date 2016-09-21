<?php

namespace Perform\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Section.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Section
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Version
     */
    protected $version;

    /**
     * @var Collection
     */
    protected $blocks;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Section
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Version $version
     *
     * @return Section
     */
    public function setVersion(Version $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param Block $block
     * @param int|null $index
     *
     * @return Section
     */
    public function addBlock(Block $block, $index = null)
    {
        $this->blocks[$index] = $block;
        $block->setSortOrder($this->blocks->indexOf($block));
        $block->setSection($this);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Return an array representation of this section and child blocks.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [];
        foreach ($this->blocks as $block) {
            $data[] = [
                'type' => $block->getType(),
                'value' => $block->getValue(),
            ];
        }

        return $data;
    }
}
