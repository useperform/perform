CRUD Events
===========

Certain events are dispatched when creating and updating entities with
Perform's admin tools.

Pre and post creation
---------------------

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
            if ($post->getAuthor() !== $this->userManager()->getUser()) {
                throw new AccessDeniedException('You are not allowed to author a post on behalf of someone else.');
            }

            $post->setDraft(true);
        }

        public function postCreate(EntityEvent $event)
        {
            $this->mailer->sendMessage('Created a new post!', $event->getEntity());
        }
    }


Pre and post update
-------------------

When an entity is updated, ``EntityEvent::PRE_UPDATE`` and
``EntityEvent::POST_UPDATE`` events are dispatched before and after it
is saved to the database.


Setting a different entity
--------------------------

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
