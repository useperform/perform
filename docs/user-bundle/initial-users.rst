Initial users
=============

The user bundle includes an :doc:`installer <../base-bundle/installers>` to add initial users to the database.

To use it, add entries to the ``perform_user.initial_users`` configuration node:

.. code-block:: yaml

    perform_user:
        initial_users:
            -
                email: user@example.com
                password: '$2y$13$vfBuBRFMxuhSTj3/T1J.jeCw4yo/cW9PnH/52AGTSdEHRsSPa1BO6'
                forename: Test
                surname: User
            -
                email: admin@example.com
                password: '$2y$13$vfBuBRFMxuhSTj3/T1J.jeCw4yo/cW9PnH/52AGTSdEHRsSPa1BO6'
                forename: Admin
                surname: User
                roles:
                    - ROLE_ADMIN

All keys are required except ``roles``, which can be used to add additional roles on top of ``ROLE_USER``, which is present by default.

Then run the ``perform:install`` command:

.. code-block:: bash

   $ ./bin/console perform:install

   Running Perform\UserBundle\Installer\UserInstaller
   # other installers

.. note::

   The installer will only add users if they don't already exist.

   It will only check for the email address existing, so a change in forename, password, etc, will not result in a new user being created.
