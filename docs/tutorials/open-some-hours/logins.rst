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

And configure the firewall in ``config/packages/security.yaml``:

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

Arkwright is quite controlling, and doesn't want Granville to be able to create, edit, or delete products.

Let's create a custom voter that only allows users with the ``ROLE_ADMIN`` role to change products.

Use the ``make:voter`` command in the maker bundle:

.. code-block:: bash

   ./bin/console make:voter ProductVoter

Now replace the ``supports`` and ``voteOnAttribute`` methods with the following:

.. code-block:: php

    use App\Entity\Product;
    use Perform\UserBundle\Entity\User;

    class ProductVoter extends Voter
    {
        protected function supports($attribute, $subject)
        {
            return $subject instanceof Product || $subject === 'product';
        }

        protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
        {
            $user = $token->getUser();
            if (!$user instanceof User) {
                return false;
            }

            if ($attribute === 'VIEW') {
                return true;
            }

            // all other actions available to Arkwright only
            return in_array('ROLE_ADMIN', $user->getRoles(), true);
        }
    }

Make sure you add use statements for the ``Product`` and ``User`` entities.

Now give Arkwright the ``ROLE_ADMIN`` role:

.. code-block:: bash

   ./bin/console perform:user:update-roles arkwright@example.com --add ROLE_ADMIN

Now login as Arkwright, and then again as Granville.

You'll notice that for Granville, the product pages are now read-only.

Field-level permissions
-----------------------

With the new security system in place, Granville is unable to fulfill one of his duties - updating stock quantities.

It'd be nice if we could allow Granville to edit **only** the ``quantity`` property, with everything else being restricted.

We can accomplish this by injecting Symfony's authorization checker to the ``ProductCrud`` class, and using the results of that to decide what fields to enable.

Update ``src/Crud/ProductCrud.php`` with the following:

.. code-block:: diff

    + use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
    + use Perform\BaseBundle\Crud\CrudRequest;

      class ProductCrud extends AbstractCrud
      {
    +     protected $authChecker;
    +
    +     public function __construct(AuthorizationCheckerInterface $authChecker)
    +     {
    +         $this->authChecker = $authChecker;
    +     }
    +
          public function configureFields(FieldConfig $config)
          {
    +         if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
    +             $config->setDefaultContexts([
    +                 CrudRequest::CONTEXT_LIST,
    +                 CrudRequest::CONTEXT_VIEW,
    +             ]);
    +         }
    +
              $config->add('name', [
                  'type' => 'string',
              ])->add('quantity', [
                  'type' => 'integer',
              ])->add('description', [
                  'type' => 'text',
              ]);
    +
    +         if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
    +             $config->add('quantity', [
    +                 'contexts' => [
    +                     CrudRequest::CONTEXT_LIST,
    +                     CrudRequest::CONTEXT_VIEW,
    +                     CrudRequest::CONTEXT_EDIT,
    +                 ],
    +             ]);
    +         }
          }
      }

And tweak the voter in ``src/Security/Voter/ProductVoter.php`` to allow Granville to edit products:

.. code-block:: diff

    - if ($attribute === 'VIEW') {
    + if ($attribute === 'VIEW' || $attribute === 'EDIT') {
          return true;
      }

Perfect! Granville can edit products now, but **only** the quantity field.

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
