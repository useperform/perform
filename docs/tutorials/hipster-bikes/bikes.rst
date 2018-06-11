Creating a bike entity
======================

Let's create a doctrine entity for bikes in our gallery, and an admin
area to manage them.

Create a doctrine entity
------------------------

Run the ``perform-dev:create:entity`` command to generate a new doctrine entity:

.. code-block:: bash

   ./bin/console perform-dev:create:entity AppBundle:Bike

The command will prompt you for field names and their types.
Declare two fields with the following database types:

* ``title`` - `string`
* ``description`` - `text`

Now update the database schema to create a new table for the entity:

.. code-block:: bash

   ./bin/console doctrine:schema:update --force --dump-sql

Create an admin
---------------

Run the ``perform-dev:create:crud`` command to create an admin class for the Bike entity:

.. code-block:: bash

   ./bin/console perform-dev:create:crud AppBundle:Bike

The command will ask for a route prefix, which is used to name the generated routes for this entity.
We'll just accept the default, ``app_crud_bikes_``.

This will create ``src/AppBundle/Crud/BikeCrud.php`` and add a service to ``app/config/services.yml``.

Define type config
------------------

Open up the newly generated ``BikeCrud`` class, containing a few empty methods:

.. code-block:: php

   <?php

    public function configureFields(FieldConfig $config)
    {
    }

    public function configureFilters(FilterConfig $config)
    {
    }

    public function configureActions(ActionConfig $config)
    {
        parent::configureActions($config);
    }

For now, we'll only deal with the ``configureFields`` method.
Add the following code:

.. code-block:: diff

      public function configureFields(FieldConfig $config)
      {
    +     $config->add('title', [
    +         'type' => 'string',
    +     ])->add('description', [
    +         'type' => 'text',
    +     ]);
      }

This tells the admin to manage the ``title`` and ``description`` properties of ``Bike``.

.. note::

   For an in-depth look at what admin classes can do, see the :doc:`crud documentation <../base-bundle/crud>`.

Create routes
-------------

We'll use Perform's ``crud`` routing type to create admin routes to manage bikes.
Add to ``app/config/routing.yml``:

.. code-block:: yaml

    bike_admin:
        resource: "AppBundle:Bike"
        type: crud
        prefix: /admin/bikes

Add a menu link
---------------

Add a new entry to ``perform_base:menu:simple`` in ``app/config/config.yml``:

.. code-block:: diff

      perform_base:
          panels:
              left: []
              right: []
          menu:
              order: []
    +         simple:
    +             bikes:
    +                 entity: "AppBundle:Bike"
    +                 icon: bicycle

Open our new admin
------------------

Visit the administration area again. You'll notice a new menu link.

Following this link will reveal an empty list of bike entities.
You can create, edit, delete, as well as view the existing bikes.
The table listing can be sorted by different columns, and bikes can be deleted in batch.

What we've created
------------------

In only a few steps, we have successfully:

* Created a new doctrine entity
* Created a crud class and service definition for that entity
* Added crud routes
* Created a menu entry for those routes

.. note::
   Rapid development is great, but what if the defaults don't work?

   Good news! We can customise and override every aspect of what we've created.
