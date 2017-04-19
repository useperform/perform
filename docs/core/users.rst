Logging in and managing users
=============================

Setting up logins
-----------------

.. note::

   If your application was created using the perform cli, it will already have logins enabled. You can skip this section.

Logging in
----------

After setting up security, you can now login to the admin area (default location is ``/admin``).

Managing users
--------------

The `PerformBaseBundle` includes an admin for managing users.

Add to ``app/config/routing.yml``:

.. code-block:: yaml

    perform_base_users:
        resource: "@PerformBaseBundle/Resources/config/routing_user.yml"
        prefix: /admin/users


Password resets
---------------

Add the following routing resource to include password reset functionality:

.. code-block:: yaml

    perform_base_passwords:
        resource: "@PerformBaseBundle/Resources/config/routing_password.yml"

Three new routes will be created for you:

.. code-block:: bash

    perform_base_password_forgot      ANY      ANY      ANY    /reset-password
    perform_base_password_reset       ANY      ANY      ANY    /reset-password/{id}/{secret}
    perform_base_password_success     ANY      ANY      ANY    /reset-password/success

.. note::


   Remember the ``/reset-password`` url path is just a default, and can be overridden to suit your needs.

   You can load this resource with a ``prefix``, or change the urls completely by copying the contents of this file into your routing configuration and changing the url paths.



A `forgot your password?` link will also appear on the login form.

A user can now request a password reset email using the new routes.

Additionally, if ``routing_user.yml`` is loaded, an admin will be able
to manage password reset tokens for all users.
