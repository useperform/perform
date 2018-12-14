Doctest
=======

Here's documentation for a type.

Documentation can continue for multiple paragraphs.

Or even another paragraph.

Included in the **PerformTools**.

Options
-------

bar
~~~
Is an array that you need to define.

This option is *required*.

**Allowed types**: ``array``

**Defaults**:

* List context: *No default*
* View context: *No default*
* Create context: *No default*
* Edit context: *No default*


foo
~~~
Docs about foo.

Something about why it's a boolean.

This option is *optional*.

**Allowed types**: ``boolean``

**Defaults**:

* List context: ``false``
* View context: ``false``
* Create context: ``false``
* Edit context: ``false``


form_options
~~~~~~~~~~~~
An array of options to pass to the underlying form type in the ``create`` and ``edit`` contexts. These will be merged with (and will overwrite) any form options that have been created as a result of the field type's other options.

This option is *optional*.

**Allowed types**: ``array``

**Defaults**:

* List context: ``[]``
* View context: ``[]``
* Create context: ``[]``
* Edit context: ``[]``


label
~~~~~
The label to use for form labels and table headings. If no label is provided, a sensible label will be created automatically.

This option is *optional*.

**Allowed types**: ``string``

**Defaults**:

* List context: *No default*
* View context: *No default*
* Create context: *No default*
* Edit context: *No default*


Example
-------

.. code-block:: php

    <?php
    $config->add('field', [
        'type' => 'doctest',
        'options' => [
            'foo' => true,
            'bar' => [true, 3, 'something'],
        ],
    ]);
