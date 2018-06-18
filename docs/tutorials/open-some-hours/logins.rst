Requiring logins
================

The products page is currently unprotected, anyone can access it!

Let's give Arkwright and Granville accounts so they can log in to manage the products.

Add the user bundle
-------------------

Add the user bundle with composer:

.. code-block:: bash

    composer require perform/user-bundle

Then update the database schema to include the users table:

.. code-block:: bash

   ./bin/console doctrine:schema:update --force --dump-sql

Set up logins
-------------

Import the login routes provided by the bundle in ``config/routes.yaml``:

.. code-block:: diff

    login:
        resource: '@PerformUserBundle/Resources/config/routing/login.yml'

And configure the firewall in ``config/packages/security.yml``:

.. code-block:: diff

      security:
          providers:
    -         in_memory: { memory: ~ }
    +         perform:
    +             entity:
    +                 class: Perform\UserBundle\Entity\User
    +                 property: email

    +     encoders:
    +         Perform\UserBundle\Entity\User:
    +             algorithm: bcrypt

          firewalls:
              dev:
                  ...
              main:
                  anonymous: true
    +             pattern: ^/
    +             form_login:
    +                 login_path: perform_user_login
    +                 check_path: perform_user_login
    +             logout:
    +                 path: perform_user_logout
    +                 target: /

          access_control:
    +           - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
    +           - { path: ^/, role: ROLE_USER }


Now refresh the page.
We've been locked out!
We now need valid credentials to access the app.

Creating users
--------------

Use the ``perform:user:create`` command to create accounts for Arkwright and Granville:

.. code-block:: bash

    $ ./bin/console perform:user:create
    Forename: Albert
    Surname: Arkwright
    Email: arkwright@example.com
    Password:
    Created user Albert Arkwright, email arkwright@example.com.

    $ ./bin/console perform:user:create
    Forename: Granville
    Surname: ?
    Email: granville@example.com
    Password:
    Created user Granville ?, email granville@example.com.

We can now login as the two shopkeepers.

Restricting to roles
--------------------

Arkwright is quite controlling, and doesn't want Granville to be able to create or edit products.

He does want Granville to be able to update the quantities however.

Let's create a custom voter that only allows users with the ``ROLE_ADMIN`` role to edit products.

Now give Arkwright the ``ROLE_ADMIN`` role:

.. code-block:: bash

   ./bin/console perform:user:update-roles arkwright@example.com --add ROLE_ADMIN

Forgotten passwords
-------------------

Silly Granville! He's forgotten his password (again).

We can reset his password using another console command:

.. code-block:: bash

   $ ./bin/console perform:user:reset-password granville@example.com
   New password:
   Updated password for user Granville ?, email granville@example.com.

It would be better if Granville could reset the password himself.

Fortunately, the user bundle includes functionality to reset passwords too.

Import a new routing file in ``config/routes.yaml``:

.. code-block:: diff

    forgot_password:
        resource: '@PerformUserBundle/Resources/config/routing/forgot_password.yml'
        prefix: /forgot-password


We also need to tweak the firewall to allow anonymous users to access the new route too:

.. code-block:: diff

      security:
          access_control:
              - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
    +         - { path: ^/forgot-password, role: IS_AUTHENTICATED_ANONYMOUSLY }
              - { path: ^/, role: ROLE_USER }

You'll now notice a 'Forgotten your password?' link on the login form that Granville can use.
He'll fill out his email and be sent a link he can use to choose a new password.
