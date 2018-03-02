Using Perform in your projects
==============================

Create a new project
--------------------

Run ``perform new <directory>`` to create a new Perform application in the given directory.

The cli tool will ask various questions about your new project,
then scaffold an entire Symfony application pre-configured with
Perform, ready to go.

Adding to an existing Symfony project
-------------------------------------

Add the required bundles to ``composer.json``, run ``composer update``
then add the bundles to your project's ``AppKernel``.

You may also want to add some of the bundles routing files.
