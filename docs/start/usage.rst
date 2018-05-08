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

Get the cli tool
----------------

You can now install the perform command line tool with composer:

.. code-block:: bash

   composer global require perform/cli dev-master

Make sure the composer bin directory is on your ``$PATH``.
For example, if using bash, you could add the following to your ``.bashrc``:

.. code-block:: bash

   PATH+=:~/.composer/vendor/bin

Then run ``perform`` to check the cli is installed, and ``perform requirements`` to check your system is configured properly.
