Access the private code repositories
====================================

.. warning::

   You need a Bespoke license to gain access to the private git repositories.

Most of Perform's code is available on `github <https://github.com/useperform>`_, however some experimental features are developed in private.

Customers with *Bespoke* licenses can access these repositories to test out upcoming features.
Additionally, any custom patches for out-of-support releases are published in these private repositories.

To get access, you need to add public ssh keys to your account and
configure composer to connect to this repo.

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

