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
     * Keys are the base entity classes, values are the child entity classes.
     *
     * @param array
     */
    protected $entities;

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

            return;
        }

        // replace any relations that target base entities with the child
        foreach ($meta->associationMappings as $property => $mapping) {
            $base = $mapping['targetEntity'];
            if (!isset($this->entities[$base])) {
                continue;
            }
            //need to unset the relation before adding it again
            unset($meta->associationMappings[$property]);

            $mapping['targetEntity'] = $this->entities[$base];
            $method = static::$mappingMethods[$mapping['type']];
            $meta->$method($mapping);
        }
    }
}
