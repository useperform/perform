Reports
=======

Reports are simple database entities used to track why a particular
item was reported for spam.

The checkers add ``Report`` instances to the ``CheckResult`` object
during the checking process, e.g. a ``JunkTextChecker`` might add a
report of type ``junk-text``.

When getting a ``CheckResult`` from the spam manager ``check*()``
methods, you could associate these reports with an entity for later
analysis.

For example, your application may have an ``InstantMessage`` entity,
allowing users to send and receive messages.
When it is sent, you could check the contents of the message with the
spam manager, saving any reports:

.. code-block:: php

    <?php

    public function newMessage(SpamManager $spamManager, InstantMessage $im)
    {
        $result = $spamManager->checkText($im->getMessage());
        if ($result->isSpam()) {
            foreach ($result->getReports() as $report) {
                // e.g. a 'junk-text' report from the JunkTextChecker
                $im->addSpamReport($report);
            }
        }
        // save the InstantMessage
    }


ReportableTrait
---------------

For ease of use, the ``ReportableTrait`` defines a ``spamReports``
property and the ``addSpamReport``, ``removeSpamReport``, and
``getSpamReports`` methods.

You must initialize a collection in the entity constructor for the
``spamReports`` property.

You'll also need to add a manyToMany mapping yourself, in order to
specify the cascade options, join table name, etc.

.. code-block:: php

    <?php

    class Message
    {
        use ReportableTrait;

        public function __construct()
        {
            $this->spamReports = new ArrayCollection();
        }
    }

.. code-block:: yaml

    # InstantMessage.orm.yml

    manyToMany:
        spamReports:
            targetEntity: Perform\SpamBundle\Entity\Report
            joinTable:
                name: instant_message_spam_reports
            cascade:
                - persist
