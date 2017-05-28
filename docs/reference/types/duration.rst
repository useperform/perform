Duration Type
===========

Use the ``duration`` type to show periods of time.

Required bundle
---------------

*PerformBaseBundle*

Available options
-----------------

format
~~~~~~

**Default**: ``DurationType::FORMAT_DIGITAL`` for list context, ``DurationType::FORMAT_VERBOSE`` for view context.

**Allowed value**: One of ``DurationType::FORMAT_DIGITAL``, ``DurationType::FORMAT_HUMAN``, or ``DurationType::FORMAT_VERBOSE``

Example
-------

.. code-block:: php

   <?php
    $config->add('length', [
        'type' => 'duration',
        'options' => [
            'format' => DurationType::FORMAT_HUMAN,
        ],
    ]);
