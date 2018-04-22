<?php

namespace Perform\BaseBundle\Tests\Fixtures\ResolveEntities\ResolveBundle\Entity;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Dog implements AnimalInterface
{
    protected $id;

    protected $isLoud = true;

    public function makeNoise()
    {
        return $this->isLoud ? 'WOOF' : 'woof';
    }
}
