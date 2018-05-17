Setup
=====

Security configuration
----------------------

A sample ``security.yml``:

.. code-block:: yaml

    security:
        encoders:
            Perform\UserBundle\Entity\User:
                algorithm: bcrypt

        providers:
            perform:
                entity:
                    class: Perform\UserBundle\Entity\User
                    property: email

        access_decision_manager:
            strategy: unanimous

        firewalls:
            dev:
                pattern:  ^/_(profiler|wdt)/
                security: false

            main:
                pattern: ^/
                form_login:
                    login_path: perform_user_login
                    check_path: perform_user_login
                    csrf_token_generator: security.csrf.token_manager
                logout:
                    path: perform_user_logout
                    target: /
                anonymous: true

        access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/forgot-password, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/, role: ROLE_USER }

Routing
-------

The bundle includes 3 routing files for different functionality.

* ``routing_login.yml`` for the login form and logout path
* ``routing_password.yml`` for password resets when a user forgets their password
* ``routing_crud.yml`` CRUD routes for the user entity

For example, to use the login/logout form and enable password resets under the ``/forgot-password`` URL prefix:

.. code-block:: yaml

    perform_user_login:
        resource: "@PerformUserBundle/Resources/config/routing_login.yml"

    perform_user_password:
        resource: "@PerformUserBundle/Resources/config/routing/forgot_password.yml"
        prefix: /forgot-password
