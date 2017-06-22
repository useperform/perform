<?php

namespace Perform\BaseBundle\Doctrine;

use Doctrine\Common\Util\ClassUtils;

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

    /**
     * Get the fully qualified classname of an entity alias or object.
     *
     * If the entity has been extended, the child entity classname will be given.
     *
     * @param string|object $entity
     *
     * @return string
     */
    public function resolve($entity)
    {
        $entity = $this->resolveNoExtend($entity);

        return isset($this->extended[$entity]) ? $this->extended[$entity] : $entity;
    }

    /**
     * Get the fully qualified classname of an entity alias or object.
     *
     * The actual entity classname will be given, even if the entity has been extended.
     *
     * @param string|object $entity
     *
     * @return string
     */
    public function resolveNoExtend($entity)
    {
        if (is_object($entity)) {
            // get the real class if the entity is a proxy
            return ClassUtils::getRealClass(get_class($entity));
        }
        if (!is_string($entity)) {
            throw new \InvalidArgumentException(sprintf('EntityResolver#resolve() requires a string or entity object, %s given.', gettype($entity)));
        }
        $entity = ClassUtils::getRealClass($entity);

        return isset($this->aliases[$entity]) ? $this->aliases[$entity] : $entity;
    }
}
