Caching
=======

Some settings managers can benefit from caching their lookups, particularly the ``doctrine`` manager.

Set the ``perform_base.settings.cache`` configuration node to the name of a service implementing ``Psr\Cache\CacheItemPoolInterface``:

.. code-block:: yaml

    framework:
        cache:
            pools:
                cache.settings:
                    adapter: cache.adapter.system

    perform_base:
        settings:
            manager: doctrine
            cache: cache.settings


The cached values will only expire when they are changed with ``setValue()``.
You can optionally set an expiry time for cached reads, in seconds:

.. code-block:: yaml

    perform_base:
        settings:
            manager: app.custom.settings
            cache: cache.settings
            cache_expiry: 300 # expire cached settings reads after 5 minutes
