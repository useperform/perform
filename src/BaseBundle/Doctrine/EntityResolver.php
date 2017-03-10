<?php

namespace Perform\BaseBundle\Doctrine;

/**
 * EntityResolver resolves references to entities that have been extended by the
 * application.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityResolver
{
    protected $aliases;
    protected $extended;

    public function __construct(array $aliases = [], array $extendedEntities = [])
    {
        $this->aliases = $aliases;
        $this->extended = $extendedEntities;
    }

    public function resolve($entity)
    {
        if (isset($this->aliases[$entity])) {
            $entity = $this->aliases[$entity];
        }

        return isset($this->extended[$entity]) ? $this->extended[$entity] : $entity;
    }
}
