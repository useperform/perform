<?php

namespace Perform\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityEvent extends Event
{
    /**
     * Called before an entity is saved for the first time, i.e. in the create context.
     */
    const PRE_CREATE = 'perform_base.entity_pre_create';

    /**
     * Called after an entity is saved for the first time, i.e. in the create context.
     */
    const POST_CREATE = 'perform_base.entity_post_create';

    /**
     * Called before an entity is updated, i.e. in the edit context.
     * It is not called when an entity is saved for the first time.
     */
    const PRE_UPDATE = 'perform_base.entity_pre_update';

    /**
     * Called after an entity is updated, i.e. in the edit context.
     * It is not called when an entity is saved for the first time.
     */
    const POST_UPDATE = 'perform_base.entity_post_update';

    protected $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param object $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
