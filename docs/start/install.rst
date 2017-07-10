Installing perform
==================

All perform code is stored in private git repositories.
To get the code, you need to add your ssh key to your account and
tell composer how to access these repos.

Make sure your computer meets the :doc:`requirements <requirements>` before proceeding.

Add your ssh key
----------------

Login to your Perform account `here </account>`_.
This page will show your licenses and ssh keys.
Click the 'Add key' button to add a new public key, either from a file
on your computer or by pasting the contents into the box directly.

Don't have a ssh key? Run ``ssh-keygen`` and follow the instructions.

Make sure you add your `public` key (e.g. ``id_rsa.pub``), not your `private` key (e.g. ``id_rsa``).

Once added, your ssh key will give you access to the Perform git repositories.

.. note::

   To check your key has been added successfully, run ``ssh pkg@useperform.com -p 404``.
   You should see a message listing the available repositories before being logged out.

Configure composer
------------------

Add the perform repository to ``~/.composer/config.json``:

.. code-block:: json

    {
        "repositories": [
            {
                "type": "composer",
                "url":  "https://useperform.com/pkg"
            }
        ]
    }

Composer will now have access to the perform packages!

.. note::

   This configures composer to fetch packages from Perform's composer repository. You can also view the available packages `here </pkg>`_.

   View the `composer documentation <https://getcomposer.org/doc/05-repositories.md>`_ for more information on how repositories work.


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
