Subscriber manager
==================

The ``Perform\MailingListBundle\SubscriberManager`` is used to save
new subscribers into your mailing list software.

It uses *connectors* to link to different mailing lists, and
*enrichers* to add subscriber metadata that exists in data you may
have about the subscriber already.

It takes instances of ``Perform\MailingListBundle\Entity\Subscriber``
and persists them in a database queue table.
Then at an arbitrary point, the manager is *flushed*.
This will *enrich* the new subscribers before passing them onto the
relevant connector and deleting them from the queue.

Add a new subscriber
--------------------

Use the ``addSubscriber()`` method to add a new subscriber to the queue.

.. code-block:: php

    <?php

    $subscriber = new Subscriber();
    $subscriber->setEmail('interested_user@example.com');
    $subscriber->setList('my_list'); // an id referencing the list in the mailing list software
    // more attributes about the subscriber
    $subscriber->setAttributes(['more_data' => 'more_values', 'is_customer' => true]);
    $subscriber->setAttribute('some_meta_data', 'value');

    $manager->addSubscriber($subscriber);

Flushing the subscriber queue
-----------------------------

Use the ``flush()`` method to process the subscribers currently known to the manager.

It will take each subscriber, pass it to the connector, and then delete it from the queue table.

.. note::

    The subscriber manager can only flush subscribers that are added in the current request or process.
    The ``processQueue()`` method can be used to load subscribers into memory again from the queue table.

Choosing when to process the queue
----------------------------------

Since connectors are likely to make network calls to other apps and
APIs, it is wise to process new subscribers outwith a normal web
request.

The bundle includes an event listener that will call ``flush()`` on the ``kernel.terminate`` event, which will fire *after* the response is sent to the browser if you use the fastcgi PHP runtime.

Alternatively, you may wish to process the queue in a different process.
The ``perform:mailing-list:process-queue`` console command will load and flush subscribers in batches.
Use the ``--batch-size`` option to configure the size of these batches to work with your memory requirements.
