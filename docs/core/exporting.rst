Exporting
=========

In the admin list context, you can export entities to a variety of formats.

Clicking the 'Download' button shows a list of available formats.
Selecting one of these will download an export of these entities in the selected format.

Required configuration
----------------------

Add ``routing_export.yml`` from the `BaseBundle` to your routing configuration:

.. code-block:: yaml

    perform_base_export:
        resource: "@PerformBaseBundle/Resources/config/routing_export.yml"
        prefix: /admin/_export

This resource loads routes for a controller in the `BaseBundle` that handles the different actions.

Make sure to use a sensible prefix that won't conflict with any existing routes, e.g. ``/admin/_export``.

Customizing exports
-------------------

Exporting options can be customized on a per-admin basis with the ``Perform\BaseBundle\Admin\AdminInterface#configureExports()`` method.

.. code-block:: php

   <?php

    public function configureExports(ExportConfig $config)
    {
    }

The passed ``Perform\BaseBundle\Config\ExportConfig`` instance can be used to customize how exporting works.

Changing the available formats
------------------------------

Use ``setFormats()`` to change the available export formats.
A valid format is one of the ``ExportConfig::FORMAT_*`` constants.

.. code-block:: php

   <?php

    public function configureExports(ExportConfig $config)
    {
        $config->setFormats([
            ExportConfig::FORMAT_JSON,
            ExportConfig::FORMAT_CSV,
            ExportConfig::FORMAT_XLS,
        ]);
    }

Use the ``configureFormat()`` method to configure a given format.

.. code-block:: php

   <?php

    public function configureExports(ExportConfig $config)
    {
        $config->configureFormat(ExportConfig::FORMAT_CSV, [
            'showHeaders' => false,
        ]);
    }

Each format has different options:

* **CSV** - ``delimiter`` (string), ``enclosure`` (string), ``escape`` (string), ``showHeaders`` (boolean), ``withBom`` (boolean)
* **XLS** - ``showHeaders`` (boolean)
* **JSON** - no options available.

Customizing the filename
------------------------

Use the ``setFilename()`` method to set the name of the downloaded file.
It can be a string, or a function that takes the name of the format and returns a string.

.. code-block:: php

   <?php

    public function configureExports(ExportConfig $config)
    {
        $config->setFilename('data'); // will return data.json, data.csv, etc
        $config->setFilename(function($format) { return 'data_'.rand(1, 100).'.'.$format; });
    }

By default, the filename will be a sensible suggestion for the current admin (e.g. ``UserAdmin`` would become ``users.json``).

Using export links elsewhere
----------------------------

Use the twig method ``perform_export_route`` to get a link to an entity export in other places.
The entity alias or classname is required, e.g. ``AppBundle:Post`` or ``AppBundle\Entity\Post``.

.. code-block:: html+twig

    <a href="{{perform_export_route('PerformUserBundle:User', 'json')}}">
        Download users as json
    </a>
