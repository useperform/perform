Manager data sources
====================

The settings manager can be backed by different data sources depending on your use case.

Doctrine
--------

The ``doctrine`` manager stores settings in a database table.

When enabled, this manager will register a doctrine mapping for the ``PerformBaseBundle:Setting`` entity.

.. code-block:: yaml

    perform_base:
        settings:
            manager: doctrine


.. note::

   You must have a Doctrine entity implementing ``Symfony\Component\Security\Core\User\UserInterface`` to use this manager.

   The user bundle provides this, or you can create your own user that implements the interface.


Parameters
----------

The ``parameters`` manager fetches settings from the service container parameters.

Use this manager when settings should only be configurable by a developer.

If certain settings could break functionality when misconfigured by the user, consider using this manager.
API keys are a good example of this.

.. code-block:: yaml

    perform_base:
        settings:
            manager: parameters

.. note::

    This manager is **read-only**.


Hybrid
------


Custom implementation
---------------------

You can also use a custom implementation by passing in the name of a service implementing ``SettingsManagerInterface``.

.. code-block:: yaml

    perform_base:
        settings:
            manager: app.custom_service # name of the manager service
