Using settings
==============

Perform makes it easy to add user-configurable settings and panels to
manage them in a friendly way.

.. note::
   Try to avoid overwhelming the user with settings.

   If an app or bundle should only be configurable by a developer,
   consider defining a dependency injection configuration instead -
   expecially if misconfiguring the setting could break functionality.

   API keys are a good example of this.

Get and set values
------------------

User-scoped settings
--------------------

Defining settings
-----------------

Managing settings with a panel
------------------------------

.. note::

   Your panel doesn't have to use the settings manager.
   For example, the account settings panel shows and updates the current User entity.
