<?php

namespace Perform\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Tag
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
     * @var Collection
     */
    protected $posts;

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
     * @return Tag
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
     * @return Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
