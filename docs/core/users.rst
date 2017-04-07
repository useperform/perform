Logging in and managing users
=============================

Setting up logins
-----------------

.. note::

   If your application was created using the perform cli, it will already have logins enabled. You can skip this section.

Logging in
----------

After setting up security, you can now login in to the admin area (default location is ``/admin``).

Managing users
--------------

The `PerformBaseBundle` includes an admin for managing users.

Add to ``app/config/routing.yml``:

.. code-block:: yaml

    perform_base_users:
        resource: "@PerformBaseBundle/Resources/config/routing_user.yml"
        prefix: /admin/users
