Types
=====

In Perform admins, `types` map entity properties to the four contexts - `list`, `view`, `create`, and `edit`.

Similar to Doctrine mappings or Symfony form types, their behaviour changes depending on the underlying data.

Usually types will show the underlying data in the `list` and `view` contexts, and present form fields for the `create` and `edit` contexts. For example, a ``password`` type might show asterisks for the `list` and `view` contexts, and show a password input for the `create` and `edit` contexts.

Types can also include css and javascript for complex UI interactions, and also include types themselves!
For example, the ``collection`` type can arbitrarily nest child admins and manage their ordering using a javascript helper.

For every admin, the types are defined in the ``configureTypes()`` method.

Available types
---------------

Many types are supplied in the PerformBaseBundle, but other bundles provide types too.

Run the ``perform:debug:types` command to list the available types:

.. code-block:: bash

   $ ./bin/console perform:debug:types

   +------------+----------------------------------------+
   | Type       | Class                                  |
   +------------+----------------------------------------+
   | string     | Perform\BaseBundle\Type\StringType     |
   | text       | Perform\BaseBundle\Type\TextType       |
   | password   | Perform\BaseBundle\Type\PasswordType   |
   | date       | Perform\BaseBundle\Type\DateType       |
   ...

Types are registered with the ``perform_base.type_registry`` service (an instance of ``Perform\BaseBundle\Type\TypeRegistry``) when the container is compiled.
You normally won't need to interact with this service directly.

Adding types
------------

``AdminInterface#configureTypes()`` takes an instance of ``Perform\BaseBundle\Type\TypeConfig``, which is used as a builder to add types for a given entity.
Add a new type with ``TypeConfig#add()``, which requires the name of the field and an array of configuration.

The field name can be anything accessible by Symfony's `property access component <http://symfony.com/doc/current/components/property_access.html>`_.
Configuration should be an array with the following properties:

* ``type`` - `string`, **required**. The type name.
* ``contexts`` - `array`. List of contexts using this type.
* ``sort`` - `boolean` or `Closure`, default true. Whether to allow sorting by this field. Pass a closure for custom sorting by this field.
* ``options`` - `array`. Options to pass to the type. Different types require different options.
* ``listOptions``, ``viewOptions``, ``createOptions``, ``editOptions`` - `array`. Options specific to a certain context.

Restricting to certain contexts
-------------------------------

Use the ``contexts`` option to restrict a type to certain contexts:

.. code-block:: php

   <?php

    public function configureTypes(TypeConfig $config)
    {
        $config->add('readOnly', [
            'type' => 'string',
            'contexts' => [
                TypeConfig::CONTEXT_LIST,
                TypeConfig::CONTEXT_VIEW,
            ],
        ]);
        $config->add('noEdit', [
            'type' => 'string',
            'contexts' => [
                TypeConfig::CONTEXT_LIST,
                TypeConfig::CONTEXT_VIEW,
                TypeConfig::CONTEXT_CREATE,
            ],
        ]);
    }

Sorting
-------

Setting the ``sort`` option to true will allow sorting by this field.
The header of that column in the table will be clickable, which will sort the list of entities by that field.

``sort`` can also be a closure, which will be passed a ``Doctrine\ORM\QueryBuilder`` instance and the sort direction as a string.

For example, consider displaying a virtual ``fullname`` property in a list context:

.. code-block:: php

   <?php

   class SomeEntity
   {
   //...
       public function getFullname()
       {
           return $this->forname . ' ' . $this->surname;
       }
   }

It's not possible to sort by this method in the database, so a custom sort function is required:

.. code-block:: php

   <?php

    public function configureTypes(TypeConfig $config)
    {
        $config->add('fullname', [
            'type' => 'string',
            'contexts' => [
                TypeConfig::CONTEXT_LIST,
            ],
            'sort' => function($qb, $direction) {
                return $qb->orderBy('e.forename', $direction)
                    ->addOrderBy('e.surname', $direction);
            },
        ]);
    }

On first load, the `list` context is completely unsorted.
Use ``TypeConfig#setDefaultSort()`` to sort by a certain field by default.

.. code-block:: php

   <?php

    public function configureTypes(TypeConfig $config)
    {
        //...
        $config->setDefaultSort('createdAt', 'DESC');
    }

Configure options per context
-----------------------------

Use the ``listOptions``, ``viewOptions``, ``createOptions``, and ``editOptions`` to change how types are configured for a certain context.

For example, here we tell the ``datetime`` type to show a human friendly date diff (e.g. `2 hours ago`) in the `list` context, but the full date in the `view` context:

.. code-block:: php

    <?php

    public function configureTypes(TypeConfig $config)
    {
        $config->add('createdAt', [
                'type' => 'datetime',
                'viewOptions' => [
                    'human' => false,
                ],
                'listOptions' => [
                    'human' => true,
                ],
            ]);
    }
