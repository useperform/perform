Spam Bundle
===========

The *PerformSpamBundle* contains tools and services for spam prevention.

Use the :doc:`spam manager <manager>` to check text, form, and
requests for signs of spam using a collection of :doc:`checkers
<checkers>`.

These checks return 0 to many :doc:`reports <reports>`, which can optionally be
associated with entities you are checking.

The bundle also includes some :doc:`form types <forms>` to
detect automated form submissions.

.. toctree::
   :maxdepth: 2

   ./manager
   ./checkers
   ./reports
   ./forms
