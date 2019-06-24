<?php

namespace Perform\BaseBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Event\OnClassMetadataNotFoundEventArgs;
use Perform\BaseBundle\Util\StringUtil;
use Doctrine\ORM\Mapping\MappingException;

/**
 * Resolve references to interfaces with concrete entities.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ResolveEntitiesListener implements EventSubscriber
{
    protected $entities;

    protected static $mappingMethods = [
        ClassMetadata::MANY_TO_MANY => 'mapManyToMany',
        ClassMetadata::MANY_TO_ONE => 'mapManyToOne',
        ClassMetadata::ONE_TO_MANY => 'mapOneToMany',
        ClassMetadata::ONE_TO_ONE => 'mapOneToOne',
    ];

    public function __construct(array $entities)
    {
        foreach ($entities as $interface => $config) {
            $this->entities[$this->sanitizeClass($interface)] = $this->sanitizeClass($config);
        }
    }

    /**
     * Remove namespace slashes to avoid problems.
     */
    private function sanitizeClass($value)
    {
        if (is_string($value)) {
            return ltrim($value, '\\');
        }
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                unset($value[$k]);
                $value[ltrim($k, '\\')] = ltrim($v, '\\');
            }
        }

        return $value;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::onClassMetadataNotFound,
            Events::loadClassMetadata,
        ];
    }

    /**
     * Directly load mapping for an entity interface, but only if it has a single implementation.
     *
     * e.g. for loading AuthorInterface:
     *
     * Valid:
     * AuthorInterface: User
     *
     * Valid:
     * AuthorInterface:
     *     Post: User
     *
     * Not Valid:
     * AuthorInterface:
     *     Post: User
     *     Comment: Visitor
     */
    public function onClassMetadataNotFound(OnClassMetadataNotFoundEventArgs $args)
    {
        $interface = $args->getClassName();
        if (!isset($this->entities[$interface])) {
            return;
        }
        if (is_array($this->entities[$interface]) && count($this->entities[$interface]) !== 1) {
            throw new \Exception(sprintf('Ambigious loading of a doctrine entity that implements %s. %s entities are defined as implementing this interface: %s. You can only load metadata explicitly for an interface if there is a single implementation.', $interface, count($this->entities[$interface]), implode($this->entities[$interface], ', ')));
        }

        $target = is_string($this->entities[$interface]) ? $this->entities[$interface] : array_values($this->entities[$interface])[0];

        $meta = $args->getObjectManager()
              ->getClassMetadata($target);

        if ($meta->isMappedSuperclass) {
            throw new MappingException(sprintf('Unable to use %s as an implementation of %s because it is a mapped superclass. Has %s been extended by another entity? If so, you should mark that entity as implementing %s instead.', $meta->name, $interface, StringUtil::classBasename($target), StringUtil::classBasename($interface)));
        }

        $args->setFoundMetadata($meta);
    }

    /**
     * Replace relations that target an interface with concrete implementations.
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $meta = $args->getClassMetadata();

        foreach ($meta->associationMappings as $property => $mapping) {
            $interface = $mapping['targetEntity'];
            if (!isset($this->entities[$interface])) {
                continue;
            }
            if (is_string($this->entities[$interface])) {
                // one class to be always used for the interface
                $target = $this->entities[$interface];
            } elseif (isset($this->entities[$interface][$meta->name])) {
                // different classes used for the interface
                // found a class for this entity relation
                $target = $this->entities[$interface][$meta->name];
            } else {
                // no class found for this relation
                continue;
            }

            //need to unset the relation before adding it again
            unset($meta->associationMappings[$property]);

            $mapping['targetEntity'] = $target;
            $method = static::$mappingMethods[$mapping['type']];
            $meta->$method($mapping);
        }
    }
}
