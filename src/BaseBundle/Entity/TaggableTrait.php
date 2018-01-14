<?php

namespace Perform\BaseBundle\Entity;

/**
 * Entities with this trait can have tags added to them.
 *
 * Child entities must set the $tags property as a doctrine collection in their constructor:
 *
 * public function __construct()
 * {
 *     $this->tags = new ArrayCollection();
 * }
 *
 * You also need to create a unidirectional ManyToMany relationship with PerformBaseBundle:Tag.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
trait TaggableTrait
{
    protected $tags;

    /**
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return object
     */
    public function addTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }
}
