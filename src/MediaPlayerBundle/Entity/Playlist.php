<?php

namespace Perform\MediaPlayerBundle\Entity;

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
}
