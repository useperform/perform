Resetting passwords
===================

Forgotten passwords
-------------------

If your users forget their login password, they can reset it using a
link sent to their email address.

Add the following routing resource to register the require routes:

.. code-block:: yaml

    # app/config/routing.yml

    forgotten_passwords:
        resource: "@PerformUserBundle/Resources/config/routing/forgot_password.yml"
        prefix: /forgot-password

Three new routes will be created for you:

.. code-block:: bash

   perform_user_forgot_password            ANY      ANY      ANY    /forgot-password/
   perform_user_forgot_password_reset      ANY      ANY      ANY    /forgot-password/reset
   perform_user_forgot_password_success    ANY      ANY      ANY    /forgot-password/success

.. note::

   The ``/forgot-password`` path prefix is just a suggestion, and can be changed to suit your needs.

A `forgot your password?` link will also appear on the login form.

A user can now request a password reset email using the new routes.

Additionally, if ``routing_admin.yml`` is loaded, an administrator will be able
to manage password reset tokens for all users.


Requiring a password reset
--------------------------

There may be moments you want to force a user to reset their password.
This can be especially useful for new accounts where you've sent them
a one-time login URL, or if you suspect an account password has been
compromised.

The ``User`` entity has a ``passwordExpiresAt`` datetime property.
When this date passes, the password is considered invalid.

Set it to a date in the past to require the user to reset their password when they next login.

.. code-block:: php

    <?php

    /* @var Perform\UserBundle\Entity\User $user */
    $user->setPasswordExpiresAt(new \DateTime('-1 day');

    /* @var Doctrine\ORM\EntityManagerInterface $em */
    $em->persist($user);
    $em->flush();


.. note::

   Whenever you use the ``UserManager`` to update a password, ``passwordExpiresAt`` will be updated to a date in the future, configured with the ``perform_user.password_valid_duration`` option.
   This is usually sensible, but if not, simply set it back to the previous value after calling ``UserManager#updatePassword``.
