Entity Type
===========

The ``entity`` type is used to assign one or many related doctrine
entities to an entity property.

For example, a ``Pet`` entity may have an ``owner`` property, a
``manyToOne`` doctrine relation to a ``User`` entity.
You would then use the ``entity`` type on this property to be able to
assign an owner to a pet.

Required bundle
---------------

*PerformBaseBundle*

Available options
-----------------

+-------------+-------+-----------+
|Option       |Default|Description|
+=============+=======+===========+
|class        |       |The related|
|             |       |entity     |
|             |       |class      |
+-------------+-------+-----------+
|display_field|id     |The        |
|             |       |property to|
|             |       |use to     |
|             |       |display    |
|             |       |the related|
|             |       |entity     |
+-------------+-------+-----------+

Note that sorting will not work out of the box.
You'll need to either disable it or define a :ref:`custom sort function <type_sorting>`.

Example
-------

.. code-block:: php

   <?php
    $config->add('owner', [
        'type' => 'entity',
        'options' => [
            'class' => 'PerformBaseBundle:User',
            'display_field' => 'email',
        ],
        'sort' => false,
    ]);
