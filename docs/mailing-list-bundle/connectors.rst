Connectors
==========

Connectors take new subscribers from the queue and add them to mailing list software.
The bundle includes some built-in connectors, or you can define your own.

If you don't have any mailing list software, you could use the
*LocalConnector* for basic list management by storing signups in a
database.
When your needs grow in the future, you can move them to software with
more features by using a different connector.

Built-in connectors
-------------------

When configured, the bundle can create connectors for you.

Add entries to the ``perform_mailing_list.connectors`` configuration option to define them:

.. code-block:: yaml

    perform_mailing_list:
        connectors:
            mailchimp:
                connector: mailchimp
                api_key: 00000000000000000000000000000000-us10
            simple:
                connector: local


The key of each ``connectors`` entry will be the name of the
connector, so the above example defines the ``mailchimp`` and
``simple`` connectors.

Custom connectors
-----------------

Alternatively, you can implement
``Perform\MailingListBundle\Connector\ConnectorInterface``, define it
as a service, and give it the ``perform_mailing_list.connector`` tag.
The ``alias`` tag option is required so you can refer to the connector by name.

.. code-block:: yaml

    app.my_connector
        class: AppBundle\Connector\MyCustomConnector
        public: false
        tags:
            - {name: perform_mailing_list.enricher, alias: 'my_connector'}


Using multiple connectors
-------------------------

If you have more than one connector configured, the subscriber manager
will use the first connector by default.
To use a different connector, set the name explicitly on the subscriber entity:

.. code-block:: php

    <?php

    $subscriber->setConnectorName('my_connector');
    $manager->addSubscriber($subscriber);
