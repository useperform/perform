Security
========

Field-level permissions
-----------------------

You may want to change the available fields depending on user's credentials.
Doing this is straightforward by injecting the Symfony AuthorizationChecker into your crud class, then using it in the ``configureFields()`` method:

.. code-block:: php

    class ProductCrud extends AbstractCrud
    {
        protected $authChecker;

        public function __construct(AuthorizationCheckerInterface $authChecker)
        {
            $this->authChecker = $authChecker;
        }

        public function configureFields(FieldConfig $config)
        {
            $config->add('name', [
                'type' => 'string',
            ]);
            if ($this->authChecker->isGranted('ROLE_ADMIN')) {
                $config->add('enabled', [
                    'type' => 'boolean',
                ]);
            }
            $config->add('description', [
                'type' => 'text',
            ]);
            if (!$this->authChecker->isGranted('ROLE_MARKETING')) {
                $config->add('description', [
                    'contexts' => [CrudRequest::CONTEXT_LIST, CrudRequest::CONTEXT_VIEW, CrudRequest::CONTEXT_EXPORT],
                ]);
            }
        }
    }

In the example above, we only show the ``enabled`` field for users with the ``ROLE_ADMIN`` role, and grant editing rights on the ``description`` field to those with the ``ROLE_MARKETING`` role.
Note that the ``description`` field can be added multiple times, replacing the default for the ``contexts`` option (all contexts) with read-only contexts if the user doesn't have the required role.

This works for most use cases, but what about restricting field only to certain entities?

CrudVoter
---------

For each crud context, the security system checks if the current user has permission to view the page.
It's also used to check if actions are available for a given entity.

By default, these checks will fail if there is no security voter registered to grant access for that entity.

Creating a voter for each entity can become tedious, so the base bundle provides an optional voter that grants access to all crud contexts for entities that have a crud service.

To use it, set the ``perform_base.security.crud_voter`` configuration node to true:

.. code-block:: yaml

    perform_base:
        security:
            crud_voter: true

You must set the access decision manager strategy to ``unanimous`` with the voter enabled, otherwise it may grant access to certain things that you have denied access to in another voter.

Set the strategy in ``security.yaml``:

.. code-block:: yaml

    security:
        access_decision_manager:
            strategy: unanimous
