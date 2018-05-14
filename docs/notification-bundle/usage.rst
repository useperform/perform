Usage
=====

Notifications are represented by the ``Perform\NotificationBundle\Notification`` class.

They require a list of recipients, a notification 'type', and an array of context variables.

Use the ``perform_notification.notifier`` service, an instance of ``Perform\NotificationBundle\Notifier\Notifier``, to send these notifications.

.. code-block:: php

    <?php

    $recipient = new User('user@example.com');
    $notification = new Notification($recipient, 'special_offer', ['expires' => new \DateTime('+7 days')]);
    /* @var Perform\NotificationBundle\Notifier\Notifier $notifier */
    $notifier->send($notification, ['sms']);

This will send a ``special_offer`` notification to our fictional
``User`` entity, passing in an `expires` datetime object to use when
sending.

Recipients must implement ``Perform\NotificationBundle\Recipient\RecipientInterface``.
The bundle supplies a ``SimpleRecipient`` class, but you'll most
likely want to have your user entity implement this interface.

The :doc:`PerformUserBundle <../user-bundle/index.rst>` User entity
also implements this interface.


Publishers
----------

The notifier delegates the actual sending of notifications to *publishers*.
Each publisher will have a different method of sending, such as email, SMS, or storing in a database.

Use the second argument of ``Notifier#send()`` to specify the publishers to send the notification with.

.. code-block:: php

    <?php

    //send using the email publisher
    $notifier->send($notification, ['email']);

    //send using the email and local publishers
    $notifier->send($notification, ['email', 'local']);

Context variables
-----------------

Different publishers require certain keys in the ``$context`` array to be set.

For example, the email publisher requires the 'subject' context variable to set the email subject.


.. code-block:: php

    <?php

    $notification = new Notification($recipient, 'welcome');
    $notifier->send($notification, ['email']);
    // error! missing subject

    $notification = new Notification($recipient, 'welcome', ['subject' => 'Welcome to our app']);
    $notifier->send($notification, ['email']);
    // notification sent successfully

Templates
---------

Most publishers will use a
``Perform\NotificationBundle\Renderer\RendererInterface`` instance to
generate the content of the notifications.

Most commonly used is the ``TwigRenderer``, which uses twig templates.

It will load ``notification/<notification_type>/<publisher_name>.html.twig``, or ``@<Bundle>/notification/<notification_type>/<publisher_name>.html.twig`` if the notification type matches the form ``<Bundle>:<type>``.

The bundle syntax allows for an application to override notification templates in a bundle.

For example, to override the ``Vendor:welcome`` notification template
for the ``email`` publisher, create
``app/Resources/VendorBundle/views/notification/welcome/email.html.twig``.

A rendered template will be passed variables from the ``$context`` array.
The following variables will be supplied too:

* ``currentRecipient`` - The recipient for this message
  (a notification may be sent to many recipients).
* ``notification`` - The notification object.
