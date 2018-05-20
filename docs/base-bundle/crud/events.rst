Events
======

Various events are dispatched at points in the CRUD lifecycle, all beginning with ``perform_base.crud.*``.

You could use these events to modify how CRUD is handled for certain entities and tweak how pages look or behave.

Context events
--------------

``Perform\BaseBundle\Event\ContextEvent`` instances are dispatched for each of the crud contexts, just before the controller returns its response:

* ``perform_base.crud.list_context`` (or ``ContextEvent::CONTEXT_LIST``) for the list context
* ``perform_base.crud.view_context`` (or ``ContextEvent::CONTEXT_VIEW``) for the view context
* ``perform_base.crud.create_context`` (or ``ContextEvent::CONTEXT_CREATE``) for the create context
* ``perform_base.crud.edit_context`` (or ``ContextEvent::CONTEXT_EDIT``) for the edit context

``ContextEvent`` instances contain the current ``CrudRequest`` and an array of template variables.

 You could use these events to modify the variables sent to the twig templates for each context.

Database events
---------------

Entity database operations
~~~~~~~~~~~~~~~~~~~~~~~~~~

``Perform\BaseBundle\Event\EntityEvent`` instances are dispatched when an entity is created, updated, or deleted:

* ``perform_base.crud.pre_create`` (or ``EntityEvent::PRE_CREATE``) before an entity is added to the database
* ``perform_base.crud.post_create`` (or ``EntityEvent::POST_CREATE``) after an entity is added to the database
* ``perform_base.crud.pre_update`` (or ``EntityEvent::PRE_UPDATE``) before an entity is updated in the database
* ``perform_base.crud.post_update`` (or ``EntityEvent::POST_UPDATE``) after an entity is updated in the database
* ``perform_base.crud.pre_delete`` (or ``EntityEvent::PRE_DELETE``) before an entity is deleted from the database
* ``perform_base.crud.post_delete`` (or ``EntityEvent::POST_DELETE``) after an entity is deleted from the database

``EntityEvent`` instances contain the current ``CrudRequest`` and the doctrine entity that is being changed.

You could use these events to modify the entity, or throw an exception if certain conditions are not met.

You could also use the ``setEntity`` method on ``EntityEvent`` to override the entity entirely:

.. code-block:: php

    <?php

    public function preCreate(EntityEvent $event)
    {
        // create a completely new entity
        $post = new Post();

        $event->setEntity($post);
    }

Of course, this technique will only work for ``PRE_CREATE``,  ``PRE_UPDATE``, and ``PRE_DELETE`` events, as they are dispatched before the entity interacts with the database.

Select queries
~~~~~~~~~~~~~~

``Perform\BaseBundle\Event\QueryEvent`` instances are dispatched when selecting database entities.

* ``perform_base.crud.list_query`` (or ``QueryEvent::LIST_QUERY``) when selecting a collection of entities for the list context
* ``perform_base.crud.view_query`` (or ``QueryEvent::VIEW_QUERY``) when selecting a single entity to examine in the view context
* ``perform_base.crud.edit_query`` (or ``QueryEvent::EDIT_QUERY``) when selecting a single entity to be added to the form in the edit context

``QueryEvent`` instances contain the current ``CrudRequest`` and a ``Doctrine\ORM\QueryBuilder`` used to build a select query.

You could use these events to change how entities are selected, e.g. adding an extra 'where' clause to the query.

Examples
--------

Restrict the selected items in the list context
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    public function onListQuery(QueryEvent $event)
    {
        // only applies when selecting Post entities
        if (!$event->getCrudRequest()->supportsEntityClass(Post::class)) {
            return;
        }

        // regular users cannot see drafts
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            $event->getQueryBuilder()->andWhere('e.draft = false');
        }
    }


Set a property and enforce business logic before creation, notify on success
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    class PostListener
    {
        public function preCreate(EntityEvent $event)
        {
            $post = $event->getEntity();
            if (!$post instanceof Post) {
                return;
            }
            if ($post->getAuthor() !== $this->currentUser()) {
                throw new AccessDeniedException('You are not allowed to author a post on behalf of someone else.');
            }

            $post->setDraft(true);
        }

        public function postCreate(EntityEvent $event)
        {
            $this->notify('Created a new post!', $event->getEntity());
        }
    }
