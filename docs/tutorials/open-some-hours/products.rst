Products
========

Let's create a doctrine entity to represent products in the inventory, and use Perform to create an interface to manage them.

Add the maker bundle
--------------------

Symfony provides a handy ``maker-bundle`` to generate files quickly.
Let's add it to create the doctrine entity quickly.

.. code-block:: bash

   composer require --dev maker-bundle

Create a doctrine entity
------------------------

Run the ``make:entity`` command to generate the Product entity:

.. code-block:: bash

   ./bin/console make:entity Product

The command will prompt you for property names and their types.
Declare three fields with the following database types:

* ``name`` - `string`
* ``quantity`` - `integer`
* ``description`` - `text`

Connecting to a database
------------------------

We now need a database to connect to.

For this tutorial, we'll use PostgreSQL running in a docker container.
You're welcome to use any database that Doctrine ORM supports.

Run Postgres in a new terminal:

.. code-block:: bash

   docker run -ti --rm --name pg -p 5432:5432 -e POSTGRES_PASSWORD=postgres postgres

Now update the doctrine configuration in ``config/packages/doctrine.yaml``:

.. code-block:: diff

      doctrine:
          dbal:
    -         # configure these for your database server
    -         driver: 'pdo_pgsql'
    -         server_version: '5.7'
    -         charset: utf8mb4
    -         default_table_options:
    -             charset: utf8mb4
    -             collate: utf8mb4_unicode_ci

              url: '%env(resolve:DATABASE_URL)%'

And update the database connection URL in ``.env``:

.. code-block:: diff

    - DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
    + DATABASE_URL=pgsql://postgres:postgres@127.0.0.1:5432/open-some-hours

Next, create the database if it doesn't exist:

.. code-block:: bash

   ./bin/console doctrine:database:create

.. note::

    If you're using PostgreSQL, make sure the ``uuid-ossp`` extension is installed:

    .. code-block:: bash

        ./bin/console doctrine:query:sql 'create extension "uuid-ossp"'

With an empty database created, we can now update the database schema to create the new products table:

.. code-block:: bash

   ./bin/console doctrine:schema:update --force --dump-sql

Enable the ``timestampable`` doctrine extension in ``config/packages/stof_doctrine_extensions.yaml``:

.. code-block:: yaml

      stof_doctrine_extensions:
          default_locale: en_US
    +     orm:
    +         default:
    +             timestampable: true

Create a crud class
-------------------

Add the following to ``src/Crud/ProductCrud.php``:

.. code-block:: php

    <?php

    namespace App\Crud;

    use Perform\BaseBundle\Crud\AbstractCrud;
    use Perform\BaseBundle\Config\FieldConfig;

    class ProductCrud extends AbstractCrud
    {
        public function configureFields(FieldConfig $config)
        {
            $config->add('name', [
                'type' => 'string',
            ])->add('quantity', [
                'type' => 'integer',
            ])->add('description', [
                'type' => 'text',
            ]);
        }
    }

This crud class manages the ``name``, ``quantity``, and ``description`` properties of ``Product``.

.. note::

   For an in-depth look at what crud classes can do, see the :doc:`crud documentation <../../base-bundle/crud/index>`.

Create routes
-------------

We'll use Perform's ``crud`` routing type to create some routes to manage products.
Add to ``config/routes.yaml``:

.. code-block:: yaml

    products:
        resource: product
        type: crud
        prefix: /products

Add a menu link
---------------

Add a new entry to ``perform_base:menu:simple`` in ``config/packages/perform_base.yaml``:

.. code-block:: diff

      perform_base:
          menu:
    +         simple:
    +             products:
    +                 crud: product
    +                 icon: "shopping-basket"

And create a label for it in ``translations/PerformBaseBundle.en.yml``:

.. code-block:: yaml

   menu:
       products: 'Products'

Enabling actions
----------------

Add to ``routes.yaml``:

.. code-block:: yaml

    actions:
        resource: '@PerformBaseBundle/Resources/config/routing/actions.yml'


Enable the crud security voter in ``config/packages/perform_base.yaml`` so basic actions like viewing, editing, and deleting are available:

.. code-block:: diff

      perform_base:
    +     security:
    +         crud_voter: true
          menu:
              simple:


To use the voter, we have to set the security strategy to ``unanimous`` in ``config/packages/security.yaml``:

.. code-block:: diff

      security:
          providers:
              in_memory: { memory: ~ }
    +     access_decision_manager:
    +         strategy: unanimous


The crud voter grants access to all entities that have a crud, for attributes like ``VIEW``, ``EDIT``, and ``DELETE``.
Without this voter, these access decisions will be denied unless you register a voter yourself.

.. note::

   Security is a deep topic that we only skim over in this tutorial.
   Don't worry if you don't understand everything that is going on here; our aim is to get up and running quickly.

Results
-------

Now head to http://localhost:8000/products to see an empty list of products.

You can view, edit, and delete existing products, as well as creating new products.
The table listing can be sorted by different columns, and widgets can be deleted in batch.

In only a few steps, we have successfully created a new product entity and generated routes to view, create, edit, and delete them.

This will be the foundation of our application; now let's customize it to fit the needs of the business.
