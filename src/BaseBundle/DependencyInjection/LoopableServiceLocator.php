<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\DependencyInjection\Definition;

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

    /**
     * Create a new service LoopableServiceLocator service definition,
     * configured with the correct tag.
     *
     * @return Definition
     */
    public static function createDefinition(array $factories)
    {
        return (new Definition(self::class))
            ->setPublic(false)
            ->addTag('container.service_locator')
            ->setArgument(0, $factories);
    }
}
