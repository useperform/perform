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

* ``model`` - `string`
* ``description`` - `text`

When prompted, declare a manyToOne doctrine relationship to ``PerformMediaBundle:File``, which we'll use to show a picture of each bike.

Now update the database schema to create a new table for the entity:

.. code-block:: bash

   ./bin/console doctrine:schema:update --force --dump-sql

Create an admin
---------------

Run the ``perform-dev:create:admin`` command to create an admin class for the Bike entity:

.. code-block:: bash

   ./bin/console perform-dev:create:admin AppBundle:Bike

The generator is smart enough to guess the types of the different fields based on the database schema, so you can simply hit ``<enter>`` for each field to accept the defaults.

The generator will also prompt for a route prefix, which you can either supply or accept the default (``app_admin_bikes_``).

As well as creating ``src/AppBundle/Admin/BikeAdmin.php``, the generator has also added a service to ``app/config/services.yml``.

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

.. code-block:: yaml

    perform_base:
        menu:
            simple:
                bikes:
                    entity: "AppBundle:Bike"
                    icon: bicycle

Open our new admin
------------------

Visit the administration area again. You'll notice a new menu link.

Following this link will reveal an empty list of our newly created bike entities.
You can create, edit, delete, as well as view the existing bikes.
The table listing can be sorted by different columns, and bikes can be deleted in batch.

What we've created
------------------

In only a few steps, we have successfully:

* Created a new doctrine entity
* Created an admin class and service definition for that entity
* Defined admin routes for the admin class
* Created a menu entry for those admin routes

.. note::
   Rapid development is great, but what if the defaults don't work?

   Good news! We can customise and override every aspect of what we've created.
