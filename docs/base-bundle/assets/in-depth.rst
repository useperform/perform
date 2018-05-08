Assets in depth
===============


Assets paths
------------

What is ``asset-paths.js``, and why does it need to be generated?

Perform keeps a record of asset namespaces (``alias.resolve`` in webpack terminology) and entrypoints (asset files to build).

Bundles can register namespaces and endpoints, so the contents of ``asset-paths.js`` changes depending on the bundles and features installed.

The base bundle registers the ``perform-base`` namespace and ``perform.scss`` and ``perform.js`` entrypoints.
Other bundles such as the media and rich-content bundles register new namespaces and entrypoints too.

Your application can also define new endpoints in configuration:

.. code-block:: yaml

    perform_base:
        assets:
            namespaces:
                'my-app': '%kernel.project_dir%/src/AppBundle/Resources'
            entrypoints:
                'my-app': 'my-app/js/app.js'

When the assets are built, these extra entrypoints will be added to
``asset-paths.js`` and used by webpack, or any other build tool you've
configured.


Understanding entrypoints
-------------------------

If you're using the Perform interface for the whole of your application, you won't need to create any extra entrypoints.
Simply add extra sass and javascript modules as required, and ``perform.css`` and ``perform.js`` will contain everything your application needs.

Things change slightly when you aren't using the Perform interface on certain pages, but want to include certain Perform features on them.

For example, you might have a page with your own template and styles, but want to include the media bundle's file selector into it.
Including the whole of ``perform.css`` and ``perform.js`` in your page will cause problems; overriding existing styles and functionality on your page.

In this case, it's better to create a new entrypoint that is *namespaced* with just the file selector code, preventing it from interfering with anything else on the page.


The Perform javascript object
-----------------------------

Perform is slightly unconventional in that it (usually) builds a single javascript file for the application, but is designed to NOT be a single page application.

The different javascript features are then 'activated' by calling various methods on a global ``window.Perform`` object when required.

For example, the media application will be contained in ``perform.js``, but will only be activated when the ``window.Perform.media.startApp()`` method is called.

.. note::

   You should always try to import required javascript modules directly without referring to the ``window.Perform`` object, e.g. ``import doSomething from <module>`` instead of ``window.Perform.<module>.doSomething()``.
