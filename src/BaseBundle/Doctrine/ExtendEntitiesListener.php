<?php

namespace Perform\BaseBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Resolve extended entities by tweaking class metadata on load.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class ExtendEntitiesListener implements EventSubscriber
{
    /**
     * Keys are the parent entity classes, values are the child entity classes.
     *
     * @param array
     */
    protected $entities;

    /**
     * Mappings to pass down from a parent to a child.
     *
     * @param array
     */
    protected $childMappings = [];

    protected static $mappingMethods = [
        ClassMetadata::MANY_TO_MANY => 'mapManyToMany',
        ClassMetadata::MANY_TO_ONE => 'mapManyToOne',
        ClassMetadata::ONE_TO_MANY => 'mapOneToMany',
        ClassMetadata::ONE_TO_ONE => 'mapOneToOne',
    ];

    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $meta = $args->getClassMetadata();

        // if extended, mark as mapped superclass
        if (isset($this->entities[$meta->name])) {
            $meta->isMappedSuperclass = true;

            //oneToMany and manyToMany relations aren't allowed on a
            //superclass, so unset and save them so the child can pick them up
            foreach ($meta->associationMappings as $property => $mapping) {
                if (in_array($mapping['type'], [ClassMetadata::ONE_TO_MANY, ClassMetadata::MANY_TO_MANY])) {
                    unset($meta->associationMappings[$property]);
                    $this->childMappings[$this->entities[$meta->name]][] = $mapping;
                }
            }
        }

        // check for a parent passing down any relations
        // this works because the parent is loaded before the child
        if (isset($this->childMappings[$meta->name])) {
            foreach ($this->childMappings[$meta->name] as $mapping) {
                $method = static::$mappingMethods[$mapping['type']];
                $meta->$method($mapping);
            }
        }

        // replace any relations that target parent entities with the child
        foreach ($meta->associationMappings as $property => $mapping) {
            $parent = $mapping['targetEntity'];
            if (!isset($this->entities[$parent])) {
                continue;
            }
            //need to unset the relation before adding it again
            unset($meta->associationMappings[$property]);

            $mapping['targetEntity'] = $this->entities[$parent];
            $method = static::$mappingMethods[$mapping['type']];
            $meta->$method($mapping);
        }
    }
}
