Security
========

Strategy
--------

You should set the access decision manager strategy to ``unanimous`` in ``security.yml``:

.. code-block:: yaml

    security:
        access_decision_manager:
            strategy: unanimous
