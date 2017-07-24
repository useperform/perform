Handling new subscribers
------------------------

The ``perform_mailing_list.manager`` is used to save new subscribers into your mailing list software.

The manager uses different *connectors* to take new subscribers from a queue and add them to different services, such as:

* DripConnector
* MailchimpConnector
* LocalConnector

If you don't have any mailing list software, you could use the *LocalConnector* to store signups locally.
When your needs grow in the future, you can move them to different software with a different connector.
