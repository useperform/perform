Email publisher
===============

The ``email`` publisher is a small wrapper around the Swiftmailer Bundle.
If the ``symfony/swiftmailer-bundle`` composer package is installed, this publisher will be available.

Configuration
-------------

.. code-block:: yaml

   perform_base:
       mailer:
           from_address: noreply@superapp.com


Send an email notification
--------------------------

This publisher requires the ``subject`` key passed into the context.

.. code-block:: php

    <?php

    $notification = new Notification($recipient, 'new-account', ['subject' => 'Welcome to your new account']);
    $notifier->send($notification, ['email']);
