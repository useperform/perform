Installation
============

Perform is designed to be added to Symfony applications with the `composer <https://getcomposer.org>`_ package manager.

Pick the bundles you need for your application, then require them in your project's ``composer.json``.

For example, to add the base bundle:

.. code-block:: bash

   composer require perform/base-bundle

.. note::

   If you use Symfony flex, the bundles will be configured automatically.
   If not, be sure to add them to your project's Kernel.

See the bundle guides for detailed instructions.

Install the cli tool
--------------------

A command-line tool is available with some time-saving features.

.. code-block:: bash

   composer global require perform/cli dev-master

Make sure the composer bin directory is on your ``$PATH``.
For example, if using bash, you could add the following to your ``.bashrc``:

.. code-block:: bash

   PATH+=:~/.composer/vendor/bin

Then run ``perform`` to check the cli is installed.

The cli has many commands, such as:

* ``perform new <directory>`` will create a new Symfony application pre-configured with the Perform bundles of your choice.
* ``perform requirements`` will check your system can run Perform effectively.

View the :doc:`cli documentation<../more/cli/index>` for a detailed guide.
