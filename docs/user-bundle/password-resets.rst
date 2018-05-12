Password resets
===============

Add the following routing resource to include password reset functionality:

.. code-block:: yaml

    perform_user_password:
        resource: "@PerformUserBundle/Resources/config/routing_password.yml"
        prefix: /reset-password

Three new routes will be created for you:

.. code-block:: bash

    perform_user_password_forgot      ANY      ANY      ANY    /reset-password
    perform_user_password_reset       ANY      ANY      ANY    /reset-password/{id}/{secret}
    perform_user_password_success     ANY      ANY      ANY    /reset-password/success

.. note::

   The ``/reset-password`` path prefix is just a suggestion, and can be changed to suit your needs.

A `forgot your password?` link will also appear on the login form.

A user can now request a password reset email using the new routes.

Additionally, if ``routing_admin.yml`` is loaded, an administrator will be able
to manage password reset tokens for all users.
