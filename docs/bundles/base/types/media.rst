Media Type
==========

Requires: PerformCmsBundle

Use the 'types' option to choose the media available.
Each entry should refer to the name of a plugin.

You may use a bare string instead of an array to use only one
plugin.

.. code-block:: php

    $config->add('image', [
        'type' => 'media',
        'options' => [
            'types' => 'image',
            // same as
            // 'types' => ['image'],
        ],
        'contexts' => [
            TypeConfig::CONTEXT_LIST,
            TypeConfig::CONTEXT_EDIT,
        ],
    ])
