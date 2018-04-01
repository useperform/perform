<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * An extension of ServiceLocator to enumerate all the available services it holds.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoopableServiceLocator extends ServiceLocator implements \IteratorAggregate
{
    protected $names;

    public function __construct(array $factories)
    {
        $this->names = array_keys($factories);
        parent::__construct($factories);
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        foreach ($this->names as $name) {
            yield $name => $this->get($name);
        }
    }
}
