Types
=====

`Types` map entity properties to the four CRUD `contexts` - `list`, `view`, `create`, and `edit`.

Similar to Doctrine mappings or Symfony form types, their behaviour changes depending on the underlying data.

Usually types will show the underlying data in the `list` and `view` contexts, and present form fields for the `create` and `edit` contexts. For example, a ``password`` type might show asterisks for the `list` and `view` contexts, and show a password input for the `create` and `edit` contexts.

Types can also include css and javascript for complex UI interactions, and also include types themselves!
For example, the ``collection`` type can arbitrarily nest child crud classes and manage their ordering using a javascript helper.

For every crud class, the types are defined in the ``configureFields()`` method.

Available types
---------------

Many types are supplied in the PerformBaseBundle, but other bundles provide types too.

Run the ``perform:debug:types`` command to list the available types:

.. code-block:: bash

   $ ./bin/console perform:debug:types

   +------------+---------------------------------------------+
   | Type       | Class                                       |
   +------------+---------------------------------------------+
   | string     | Perform\BaseBundle\FieldType\StringType     |
   | text       | Perform\BaseBundle\FieldType\TextType       |
   | password   | Perform\BaseBundle\FieldType\PasswordType   |
   | date       | Perform\BaseBundle\FieldType\DateType       |
   ...

Types are registered with the ``perform_base.field_type_registry`` service (an instance of ``Perform\BaseBundle\FieldType\FieldTypeRegistry``) when the container is compiled.
You normally won't need to interact with this service directly.

Using types
-----------

``CrudInterface#configureFields()`` takes an instance of ``Perform\BaseBundle\Config\FieldConfig``, which is used as a builder to add types for a given entity.
Add a new type with ``FieldConfig#add()``, which requires the name of the field and an array of configuration.

The field name can be anything accessible by Symfony's `property access component <http://symfony.com/doc/current/components/property_access.html>`_.
Configuration should be an array with the following properties:

* ``type`` - `string`, **required**. The type name.
* ``contexts`` - `array`. A list of contexts using this type. Each item should be one of ``CrudRequest::CONTEXT_LIST``, ``CrudRequest::CONTEXT_VIEW``, ``CrudRequest::CONTEXT_CREATE``, ``CrudRequest::CONTEXT_EDIT``, or ``CrudRequest::CONTEXT_EXPORT``. Defaults to all contexts.
* ``sort`` - `boolean` or `Closure`. Whether to allow sorting by this field. Pass a closure for custom sorting by this field. Defaults to true.
* ``options`` - `array`. Options to pass to the type. Different types require different options.
* ``listOptions``, ``viewOptions``, ``createOptions``, ``editOptions`` - `array`. Options specific to a certain context.
* ``template`` - `string`. Override the twig template used to render this type. Will only apply to this entity field.

Restricting to certain contexts
-------------------------------

Use the ``contexts`` option to restrict a type to certain contexts:

.. code-block:: php

   <?php

    public function configureFields(FieldConfig $config)
    {
        $config->add('readOnly', [
            'type' => 'string',
            'contexts' => [
                CrudRequest::CONTEXT_LIST,
                CrudRequest::CONTEXT_VIEW,
            ],
        ]);
        $config->add('noEdit', [
            'type' => 'string',
            'contexts' => [
                CrudRequest::CONTEXT_LIST,
                CrudRequest::CONTEXT_VIEW,
                CrudRequest::CONTEXT_CREATE,
            ],
        ]);
    }

.. _type_sorting:

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
           return $this->forename . ' ' . $this->surname;
       }
   }

It's not possible to sort by this method in the database, so a custom sort function is required:

.. code-block:: php

   <?php

    public function configureFields(FieldConfig $config)
    {
        $config->add('fullname', [
            'type' => 'string',
            'contexts' => [
                CrudRequest::CONTEXT_LIST,
            ],
            'sort' => function($qb, $direction) {
                return $qb->orderBy('e.forename', $direction)
                    ->addOrderBy('e.surname', $direction);
            },
        ]);
    }

On first load, the `list` context is completely unsorted.
Use ``FieldConfig#setDefaultSort()`` to sort by a certain field by default.

You may also pass in a field that has not been added to the type config, which will be treated as if it had been added with the ``sort`` option set to ``true``.

.. code-block:: php

   <?php

    public function configureFields(FieldConfig $config)
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

    public function configureFields(FieldConfig $config)
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

Change the default contexts
----------------------------

Adding a field without the ``contexts`` option will enable it for all contexts by default.

You can change this default by calling ``setDefaultContexts()``:

.. code-block:: php

    public function configureFields(FieldConfig $config)
    {
        // fields are read-only by default
        $config->setDefaultContexts([
            CrudRequest::CONTEXT_LIST,
            CrudRequest::CONTEXT_VIEW,
        ]);
        $config->add('title', [
            'type' => 'string',
        ]);
    }

.. note::

   The new defaults will only apply to ``add()`` invocations after this method has been called, so you should probably place it at the top of the ``configureFields()`` method.

   You may call this method multiple times.
   Each call to ``add()`` will use the latest given defaults.

Creating a new field type
-------------------------

Create a service that implements ``Perform\Base\FieldType\FieldTypeInterface``, either through autowiring or manually.

If the service is autoconfigured, the type will be added to the registry automatically.

If the service is not autoconfigured, give the service the ``perform_base.field_type`` tag.

The name of the type will be guessed from the class name, or you can set it manually with the ``alias`` tag option.

.. code-block:: yaml

    # configured automatically
    MyApp\Type\AutoType
        autoconfigure: true

    # configured manually
    MyApp\Type\ManualType
        tags:
            - {name: perform_base.field_type}

    # configured manually, setting the type name explicitly
    MyApp\Type\AnotherType
        tags:
            - {name: perform_base.field_type, alias: app_another}


Then run the ``perform:debug:types`` command to view your new types:

.. code-block:: bash

   $ ./bin/console perform:debug:types

   +-------------+-------------------------+
   | Type        | Class                   |
   +-------------+-------------------------+
   | auto        | MyApp\Type\AutoType     |
   | manual      | MyApp\Type\ManualType   |
   | app_another | MyApp\Type\AnotherType  |
   ...
