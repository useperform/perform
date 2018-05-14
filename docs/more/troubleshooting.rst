Troubleshooting
===============

Here are some things to try when things aren't working.

Services are missing or changed
-------------------------------

Check the container tab in the logger profiler panel, looking especially for logs from compiler passes in the ``Perform\`` namespace.
They will often log why a service was removed or manipulated, usually because of a missing composer package.
