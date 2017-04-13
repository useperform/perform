UtilExtension
=============

Miscellaneous utilities.

.. note::

   Included in the base bundle.

Functions
---------

perform_route_exists
^^^^^^^^^^^^^^^^^^^^

Check if a route exists.
Useful for optionally showing a link to a route.

.. code-block:: html+twig

    {% if perform_route_exists('my_route') %}
    <a href="{{path('my_route')}}">Click here</a>
    {% endif %}

Filters
-------

perform_human_date
^^^^^^^^^^^^^^^^^^

Print a ``DateTime`` object as a phrase relative to the current time, e.g. '2 days ago' or '5 years from now'.

.. code-block:: html+twig

   <p>{{date()|perform_human_date}}</p>
