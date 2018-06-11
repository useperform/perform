Creating a crud class
=====================

All crud classes must implement ``Perform\BaseBundle\Crud\CrudInterface`` and define services tagged with ``perform_base.crud``.
If a service implementing this interface is auto-configured, the tag will be added for you.

Implementing ``CrudInterface`` requires creating several methods.
For most cases, extending ``Perform\BaseBundle\Crud\AbstractCrud`` saves coding time.
The only required code is implementing ``configureFields()``.

Suppose we created the Doctrine entity ``AppBundle\Entity\Bike``, with the fields ``model`` (a string) and ``wheelCount`` (an integer).

An example crud class could be:

.. code-block:: php

   <?php

    class BikeCrud extends AbstractCrud
    {
        public function configureFields(FieldConfig $config)
        {
            $config
                ->add('model', ['type' => 'string'])
                ->add('wheelCount', ['type' => 'integer'])
                ;
        }
    }

.. note::

   What are `types` anyway? Read more in the :doc:`next chapter <types>`.

Service tag
-----------

To tell perform about the crud, it needs to be a service with the ``perform_base.crud`` tag:

.. code-block:: yaml

    AppBundle\Crud\BikeCrud:
        tags:
            - {name: perform_base.crud, crud_name: "bike"}

This will define a crud named ``bike`` that you will reference elsewhere.

This name must be unique across your application, but you can use multiple tags with different names for a single service (e.g. for routing multiple times, see below).

.. note::

    If you autowire and auto-configure your services, Perform will normally do the 'right' thing and configure your service with a sensible ``crud_name``.

To see the new crud definition, run the ``perform:debug:crud`` console command:

.. code-block:: bash

   $ ./bin/console perform:debug:crud

   +------+-------------------------+-----------------------+
   | Name | Class                   | Entity Class          |
   +------+-------------------------+-----------------------+
   | bike | AppBundle\Crud\BikeCrud | AppBundle\Entity\Bike |
   ...

Routing
-------

The base bundle provides a ``crud`` route loader to create routes for a crud service.

To add routes for the ``bike`` crud we defined earlier:

.. code-block:: yaml

    # app/config/routing.yml
    bike_crud:
        resource: bike
        type:     crud
        prefix:   "/admin/bikes"

Be sure to include a ``prefix`` for the routes.

This will create new routes:

.. code-block:: bash

   $ ./bin/console debug:router | grep bike

     bike_list            ANY      ANY      ANY    /admin/bikes/
     bike_view            ANY      ANY      ANY    /admin/bikes/view/{id}
     bike_create          ANY      ANY      ANY    /admin/bikes/create
     bike_edit            ANY      ANY      ANY    /admin/bikes/edit/{id}

Visiting ``/admin/bikes`` in your browser will reveal a complete CRUD interface for the bike entity!

Contexts
--------

The four letters of CRUD are mapped to different 'contexts':

* The `list` context for displaying a list of entities (the read in CRUD)
* The `view` context for inspecting a single entity (the read in CRUD)
* The `create` context for creating a new entity (the create in CRUD)
* The `edit` context for editing a single entity (the update in CRUD)

By default, routing a crud service gives you all of these routes.

What about the delete in CRUD? See :doc:`actions`.

You'll learn more about contexts, and how to customise each of them, in :doc:`types`, :doc:`filters`, and :doc:`actions`.

Customising routing
-------------------

The behaviour of crud routing can be customised by tweaking the ``perform_base.crud`` service tag.

.. note::

   If the tag has been auto-configured, you'll need to explicitly create it to add these customisations.


Changing contexts
~~~~~~~~~~~~~~~~~

*List*, *view*, *create*, and *edit* contexts are nice, but we may not need all of them for every crud service.
To change them, explicitly set the ``{list|view|create|edit}_context`` attributes on the service tag.

The values of these attributes are the URL fragments to use on the route, which default to:

* ``list_context`` - ``/``
* ``view_context`` - ``/view/{id}``
* ``create_context`` - ``/create``
* ``edit_context`` - ``/edit/{id}``

When none of these contexts are set on the tag, all contexts will be used.
Otherwise, only the defined contexts will have routes created for them.

For example, this tag:

.. code-block:: yaml

    AppBundle\Crud\BikeCrud:
        tags:
            - {name: perform_base.crud, crud_name: "bike", list_context: "/", view_context: "/inspect/{id}"}

would create these routes:

.. code-block:: bash

     bike_list            ANY      ANY      ANY    /admin/bikes/
     bike_view            ANY      ANY      ANY    /admin/bikes/inspect/{id}


Changing route prefix
~~~~~~~~~~~~~~~~~~~~~

Sometimes the generated route names may conflict with other routes in the application.
In our case, the bike crud is designed for an 'admin area', and messes with public routes for viewing bikes.

Use the ``route_prefix_name`` tag attribute to change the names of the generated routes.

.. code-block:: yaml

    AppBundle\Crud\BikeCrud:
        tags:
            - {name: perform_base.crud, crud_name: "bike", route_name_prefix: "admin_bike_"}

.. code-block:: bash

     admin_bike_list            ANY      ANY      ANY    /admin/bikes/
     admin_bike_view            ANY      ANY      ANY    /admin/bikes/view/{id}
     admin_bike_create          ANY      ANY      ANY    /admin/bikes/create
     admin_bike_edit            ANY      ANY      ANY    /admin/bikes/edit/{id}


Routing a crud service many times
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A crud *name* may only be routed once, but there is no limit to the number of tags you can add to the same service.

For example, to route the list context of our bike crud to another url:

.. code-block:: yaml

    AppBundle\Crud\BikeCrud:
        tags:
            - {name: perform_base.crud, crud_name: "bike"}
            - {name: perform_base.crud, crud_name: "extra_bike", list_context: "/"}

.. code-block:: yaml

    # app/config/routing.yml
    bike_crud:
        resource: bike
        type:     crud
        prefix:   "/admin/bikes"

    extra_bike_list:
        resource: extra_bike
        type:     crud
        prefix:   "/admin/extra-bike-listing"

Here we defined the ``extra_bike`` crud name and routed the list context to it, giving these routes:

.. code-block:: bash

     bike_list               ANY      ANY      ANY    /admin/bikes/
     bike_view               ANY      ANY      ANY    /admin/bikes/view/{id}
     bike_create             ANY      ANY      ANY    /admin/bikes/create
     bike_edit               ANY      ANY      ANY    /admin/bikes/edit/{id}
     extra_bike_list         ANY      ANY      ANY    /admin/extra-bike-listing/

Debug bar
---------

When visiting a route managed by a crud class, you'll notice a new item on the debug bar:

.. image:: debug_bar.png

Clicking on this data collector shows useful information about the
active crud class, as well as some general information about all loaded
crud classes.

.. image:: data_collector.png

Overriding templates
--------------------

The template used for a crud context can be overridden in many
different ways.

Here are all the possible ways of overriding a template, in order of priority:

Return a response object from the controller
--------------------------------------------

If an action returns a Symfony ``Response`` object, no template is
needed.

Use twig explicitly or with an annotation
-----------------------------------------

Use ``render()`` or the ``@Template`` annotation to
explicitly render a template in the controller action.

Implement getTemplate() in the crud class
-----------------------------------------

A crud class may implement ``CrudInterface#getTemplate()`` to return a
custom template name.

Place a file in a specific location
-----------------------------------

The template ``@<Bundle>/crud/<entity>/<context>.html.twig`` will be used
automatically if available,
e.g. ``@PerformContact/crud/message/view.html.twig``.

Note that the entity class will be snake cased, so the entity ``BookPublisher`` in the ``AppBundle`` will search for ``@App/crud/book_publisher/list.html.twig`` in the ``list`` context.

Default
-------

If nothing else has been specified, the template
``@PerformBase/crud/<context>.html.twig`` will be used.
