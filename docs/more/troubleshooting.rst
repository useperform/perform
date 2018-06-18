Troubleshooting
===============

Here are some things to try when things aren't working.

Doctrine MappingException: "Class does not exist"
-------------------------------------------------

This cryptic error is thrown by doctrine when an interface hasn't been resolved to a mappable entity.

For example, if there is no entity registered that implements Symfony's ``UserInterface``, but there are entities registered that have an association to that interface, you'll see the following:

.. code-block:: bash

    Class 'Symfony\Component\Security\Core\User\UserInterface' does not exist

You'll need to define a entity in ``perform_base:doctrine:resolve``:

.. code-block:: yaml

    perform_base:
        doctrine:
            resolve:
                Symfony\Component\Security\Core\User\UserInterface: App\Entity\User

Note that the user bundle does this automatically for its User entity.

See :doc:`../base-bundle/doctrine/resolve` for more information.

Services are missing or changed
-------------------------------

Check the container tab in the logger profiler panel, looking especially for logs from compiler passes in the ``Perform\`` namespace.
They will often log why a service was removed or manipulated, usually because of a missing composer package.
