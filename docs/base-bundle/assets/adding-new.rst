Building your own assets
========================

We can build assets from the Perform bundles, but what about our own?

Perform exposes a few configuration nodes to add your own assets to the build:

* ``perform_base.assets.entrypoints``
* ``perform_base.assets.namespaces``
* ``perform_base.assets.extra_js``
* ``perform_base.assets.extra_sass``
* ``perform_base.assets.theme``

.. note::

    It's not required to build your assets in the same way the Perform assets are built.
    You're welcome to build your own assets in a different way to suit your needs.

Define an entrypoint
--------------------

Define a namespace
------------------

Add a javascript module
-----------------------

``perform.js`` can import javascript files, and attach the exported functions to the ``window.Perform`` object.

To include the file ``src/AppBundle/Resources/js/functions.js`` under the ``window.Perform.myApp`` property:

.. code-block:: yaml

    perform_base:
        assets:
            namespaces:
                'my-app': '%kernel.project_dir%/src/AppBundle/Resources'
            extra_js:
                'myApp': 'my-app/js/functions.js'

Include extra sass files
------------------------

``perform.scss`` can include extra scss files for styling your own markup and tweaking existing styles.

To include the file ``src/AppBundle/Resources/scss/_extras.scss`` in the build:

.. code-block:: yaml

    perform_base:
        assets:
            namespaces:
                'my-app': '%kernel.project_dir%/src/AppBundle/Resources'
            extra_sass:
                - '~my-app/scss/_extras'

.. note::

   If you're looking to change the entire look and feel of the interface, create a :doc:`theme <./themes>`.


