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
    protected $extended;

    public function __construct(array $extendedAliases = [], array $extendedEntities = [])
    {
        $this->extended = array_merge($extendedAliases, $extendedEntities);
    }

    public function resolve($entity)
    {
        return isset($this->extended[$entity]) ? $this->extended[$entity] : $entity;
    }
}
