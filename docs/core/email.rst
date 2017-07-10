Sending emails
==============

The BaseBundle provides a small wrapper around the Swiftmailer bundle, which uses twig to create the email content.

Configuration
-------------

.. code-block:: yaml

   perform_base:
       mailer:
           from_address: noreply@superapp.com

Send an email
-------------

Call the ``send()`` method on the ``perform_base.email.mailer`` service, passing in the recipient, subject line, twig template name, and any variables to pass to the template.

.. code-block:: php

    <?php

    $mailer->send('user@example.com',
        'Welcome to our application',
        'AppBundle:Email:welcome.html.twig',
        ['user' => $user]
    );

Excluding domains
-----------------

Disabling the mailer
--------------------

If you'd prefer to send emails another way, you can simply use the Swiftmailer bundle to send emails.

You can also disable the mailer service by removing the ``perform_base.mailer`` configuration node.
Be aware that this may remove other services too, such as the NotificationBundle's `email` publisher.
