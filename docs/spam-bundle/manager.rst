Spam manager
============

The ``Perform\SpamBundle\SpamManager`` is the main service provided by the bundle.

All the available :doc:`spam checkers <checkers>` are used by the
manager, making it easily extensible.

It has 3 methods to examine text, forms, and requests.
Each method returns a ``CheckResult``, which stores a collection of
``Reports``.
If a result has any reports, it is considered spam.

Checking text
-------------

.. code-block:: php

   <?php

    $result = $manager->checkText('I am spam');

    echo $result->isSpam() // true
    echo $result->getReports() // array of Report instances

Checking a form
---------------

.. note::

   The form checkers will only be called if the form has been submitted.

Checking a request
------------------
