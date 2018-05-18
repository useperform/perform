CRUD Events
===========

Certain events are dispatched at various points in the CRUD lifecycle.

Context events
--------------

``Perform\BaseBundle\Event\ListContextEvent`` events are dispatched for each of the crud contexts.

List queries
~~~~~~~~~~~~

In the list context, ``Perform\BaseBundle\Event\ListQueryEvent`` events are dispatched when querying the database.
This event can be used to adjust the query, or change it entirely.

Entity events
-------------

``Perform\BaseBundle\Event\EntityEvent`` events are dispatched when an entity is saved or removed from the database.

Pre and post creation
~~~~~~~~~~~~~~~~~~~~~

``EntityEvent::PRE_CREATE`` and ``EntityEvent::POST_CREATE`` events
are dispatched before and after an entity is saved for the first time.

You can use these events to modify the entity, or throw an exception
if certain conditions are not met.

.. code-block:: php

    <?php
    class EntityListener
    {
        public function preCreate(EntityEvent $event)
        {
            $post = $event->getEntity();
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


Pre and post update
~~~~~~~~~~~~~~~~~~~

When an entity is updated, ``EntityEvent::PRE_UPDATE`` and
``EntityEvent::POST_UPDATE`` events are dispatched before and after it
is saved to the database.


Setting a different entity
~~~~~~~~~~~~~~~~~~~~~~~~~~

You can use the ``setEntity`` method on
``Perform\BaseBundle\Event\EntityEvent`` to override the entity to be
saved entirely.

.. code-block:: php

    <?php

    public function preCreate(EntityEvent $event)
    {
        // create a completely new entity
        $post = new Post();

        $event->setEntity($post);
    }

Of course, this technique will only work for ``PRE_CREATE`` and
``PRE_UPDATE`` events, as they are dispatched before the entity is
saved to the database.
