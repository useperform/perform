<?php

namespace Perform\MediaPlayerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Playlist
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Playlist
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Collection
     */
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     *
     * @return Playlist
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param PlaylistItem $item
     *
     * @return Playlist
     */
    public function addItem(PlaylistItem $item)
    {
        $this->items[] = $item;
        $item->setPlaylist($this);

        return $this;
    }

    /**
     * @param PlaylistItem $item
     *
     * @return Playlist
     */
    public function removeItem(PlaylistItem $item)
    {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getItems()
    {
        return $this->items;
    }
}
