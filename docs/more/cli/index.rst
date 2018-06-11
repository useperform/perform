Cli
===

The Perform cli is a commandline tool to speed up your development time and automate repetitive tasks.

It can:

* Create new applications
* Manage project licenses
* Configure ssh keys

This tool is optional, but can greatly increase your productivity.

Discovering commands
--------------------

.. note::

   The Cli is built using Symfony's `console component <http://symfony.com/doc/current/components/console.html>`_, and behaves like any other Symfony-based commandline tool.

``perform list`` will show the available subcommands, with short descriptions for each.

You can get detailed help for a single command by using either the ``help`` subcommand or ``--help`` option flag.

.. code-block:: bash

   perform help create:bundle
   # same as
   perform create:bundle --help

.. toctree::

   new
