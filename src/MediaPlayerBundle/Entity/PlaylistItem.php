<?php

namespace Perform\MediaPlayerBundle\Entity;

use Perform\MediaBundle\Entity\File;

/**
 * PlaylistItem
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistItem
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var int
     */
    protected $sortOrder;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Playlist
     */
    protected $playlist;

    /**
     * @var File
     */
    protected $file;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $sortOrder
     *
     * @return PlaylistItem
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param Playlist $playlist
     *
     * @return PlaylistItem
     */
    public function setPlaylist(Playlist $playlist)
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * @return Playlist
     */
    public function getPlaylist()
    {
        return $this->playlist;
    }

    /**
     * @param File $file
     *
     * @return PlaylistItem
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $title
     *
     * @return PlaylistItem
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
}
