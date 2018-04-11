<?php

namespace Perform\RichContentBundle\DataFixtures\Profile;

/**
 * Represents a 'profile' of dummy rich content for other entities to
 * use, e.g. an 'article body' profile or a 'jumbotron' profile
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ProfileInterface
{
    /**
     * @return Perform\RichContentBundle\Entity\Block[]
     */
    public function generateBlocks();

    /**
     * @return array
     */
    public function getRequiredBlockTypes();

    /**
     * @return string
     */
    public static function getName();
}
