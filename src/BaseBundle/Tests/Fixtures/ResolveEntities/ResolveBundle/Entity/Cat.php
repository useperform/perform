<?php

namespace Perform\BaseBundle\Tests\Fixtures\ResolveEntities\ResolveBundle\Entity;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Cat
{
    protected $id;

    protected $isFriendly = true;

    public function makeNoise()
    {
        return $this->isFriendly ? 'meow' : 'hiss';
    }
}
