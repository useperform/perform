<?php

namespace Perform\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityEvent extends Event
{
    /**
     * Called before an entity is saved for the first time, e.g. in the create context.
     */
    const PRE_CREATE = 'perform_base.crud.pre_create';

    /**
     * Called after an entity is saved for the first time, e.g. in the create context.
     */
    const POST_CREATE = 'perform_base.crud.post_create';

    /**
     * Called before an entity is updated, e.g. in the edit context.
     * It is not called when an entity is saved for the first time.
     */
    const PRE_UPDATE = 'perform_base.crud.pre_update';

    /**
     * Called after an entity is updated, e.g. in the edit context.
     * It is not called when an entity is saved for the first time.
     */
    const POST_UPDATE = 'perform_base.crud.post_update';

    /**
     * Called before an entity is deleted, e.g. in the delete action.
     */
    const PRE_DELETE = 'perform_base.crud.pre_delete';

    /**
     * Called after an entity is deleted, e.g. in the deleted context.
     */
    const POST_DELETE = 'perform_base.crud.post_delete';

    protected $crudRequest;
    protected $entity;

    public function __construct(CrudRequest $crudRequest, $entity)
    {
        $this->crudRequest = $crudRequest;
        $this->entity = $entity;
    }

    /**
     * @return CrudRequest
     */
    public function getCrudRequest()
    {
        return $this->crudRequest;
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
