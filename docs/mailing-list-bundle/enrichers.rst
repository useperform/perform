Enrichers
=========

Enrichers are services that attempt to add useful metadata to incoming subscribers before they are processed.

For example, if you already store email addresses elsewhere, there could be related data that would be useful to add.
Imagine you have a CRM with customer details.
If a new mailing list signup comes in with an email address already in the CRM, an enricher could fetch some CRM data and set it as attributes on the subscriber.
Then your mailing list software will have access to this data too, which could be useful for marketing campaigns, segmentation, etc.

Built-in enrichers
------------------

When using the PerformUserBundle, the ``UserEnricher`` will search for
any new subscribers that are also users, and add the first and last
name attributes to them.

Custom enrichers
----------------

Implement ``Perform\MailingListBundle\Enricher\EnricherInterface``, define it
as a service, and give it the ``perform_mailing_list.enricher`` tag.

.. code-block:: yaml

    app.my_enricher
        class: AppBundle\Enricher\MyCustomEnricher
        public: false
        tags:
            -  perform_mailing_list.enricher
