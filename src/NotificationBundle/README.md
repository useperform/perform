# NotificationBundle

Bundle for sending notifications to users in an application using a variety of publishers.

## Usage

The `admin_notification.notifier` service is used to send notifications.

Create an instance of
`Admin\NotificationBundle\Notification`, then use the
`Admin\NotificationBundle\Notifier` to send it.

Notification is an immutable value object that requires an array of
recipients (implementing the `Admin\NotificationBundle\Recipient`
interface), the notification type, and any context variables relevant
to the notification type.

You can specify the publishers to use, or leave empty to use the
default publishers.

```php
//send using the default publishers
$notification = new Notification($recipient, 'test', ['context' => 'some_context_variable']);
$notifier->send($notification);
```

```php
//send using the local and email publishers
$notification = new Notification($recipient, 'test', ['context' => 'some_context_variable']);
$notifier->send($notification, ['local', 'email']);
```

```php
//send to multiple recipients
$notification = new Notification([$recipient, $recipient2], 'test', ['context' => 'some_context_variable']);
$notifier->send($notification, ['local', 'email']);
```

### Templates

The 'local' and 'email' publishers render notifications using twig,
with the context variables passed in. Additionally, the following
variables are set automatically:

* currentRecipient - The identifier of the recipient for this message
  (a notification may be sent to many recipients).
* notification - The notification object

The template to use is decided by looking in the following
locations:

* AdminNotificationBundle:<type>:<publisher>.html.twig
* AdminNotificationBundle:<type>:general.html.twig

For example, sending a notification with type 'new_task', using the
'email' publisher:

* AdminNotificationBundle:new_task:email.html.twig
* AdminNotificationBundle:new_task:general.html.twig

To add a template without modifying this bundle, place them in
`app/Resources/AdminNotificationBundle/views`.

e.g. `AdminNotificationBundle:new_task:email.html.twig` ->
`app/Resources/AdminNotificationBundle/views/new_task/email.html.twig`.

### Adding a notification publisher

Implement `Admin\NotificationBundle\Publisher\PublisherInterface`.
