<?php

namespace Perform\RichContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * A section of rich content built from many blocks.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class Content
{
    /**
     * @var guid
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var Collection
     */
    protected $blocks;

    /**
     * @var array
     */
    protected $blockOrder = [];

    /**
     * Used to cache block lookups by id
     */
    protected $blockIndex = [];

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * Set id.
     *
     * @param guid $id
     *
     * @return Content
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return guid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Content
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Content
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param Block    $block
     * @param int|null $index
     *
     * @return Section
     */
    public function addBlock(Block $block)
    {
        //a block can be used multiple times in the same piece of content
        $this->blockOrder[] = $block->getId();

        //but only referenced once
        if ($this->blocks->contains($block)) {
            return;
        }

        if (!$block->getId()) {
            throw new \Exception('A block must be saved before adding it to content.');
        }

        $this->blocks[] = $block;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    private function getBlockIndex()
    {
        if (empty($this->blockIndex)) {
            foreach ($this->blocks as $block) {
                $this->blockIndex[$block->getId()] = $block;
            }
        }

        return $this->blockIndex;
    }

    /**
     * @return Block[]
     */
    public function getOrderedBlocks()
    {
        $blocks = [];
        $index = $this->getBlockIndex();
        foreach ($this->blockOrder as $id) {
            $blocks[] = $index[$id];
        }

        return $blocks;
    }

    /**
     * @return array
     */
    public function getBlockOrder()
    {
        return $this->blockOrder;
    }
}
