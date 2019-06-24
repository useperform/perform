Requirements
============

Perform requires the following to run correctly:

* PHP 5.6 or later
* The dom, gd, pdo, xml, and zip PHP extensions
* The PHP extension for your database of choice, e.g. pdo_mysql
* The `composer <https://getcomposer.org>`_ package manager
* Node.js 5 or later, to install and build the css and javascript files

Recommendations
---------------

* The `yarn <https://yarnpkg.com>`_ package manager for faster npm installs

.. note::

   If you have the :doc:`cli tool <../more/cli>` installed, the
   ``perform requirements`` command can be used to check if your system meets
   the requirements.

Quickstart for common operating systems
---------------------------------------

Here are some basic commands to install the requirements.
They're not particularly robust or secure, but can help you get started quickly.

Ubuntu 16.04 LTS
~~~~~~~~~~~~~~~~

Create the file ``/etc/apt/sources.list.d/nodesource.list`` containing:

.. code-block:: conf

   deb https://deb.nodesource.com/node_6.x xenial main
   deb-src https://deb.nodesource.com/node_6.x xenial main

Then run

.. code-block:: bash

   curl -s https://deb.nodesource.com/gpgkey/nodesource.gpg.key | sudo apt-key add -
   sudo apt-get update

   sudo apt-get install php php-zip php-xml php-gd php-sqlite3 nodejs

   curl https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer

   sudo npm install -g yarn
