Actions
=======

On top of viewing, creating, editing, and deleting entities, you may
want to define operations that change entities in some way.

Actions are a way of doing this without having to create a new
controller and route for every action.

All actions must implement
``Perform\BaseBundle\Action\ActionInterface``, and define services
tagged with ``perform_base.action``.

``ActionInterface#run()`` should return an instance of
``Perform\BaseBundle\Action\ActionResponse`` or throw an exception.

This method must accept an array of 0, 1, or many
entities, so make sure your action accounts for this.

Create an action
----------------

Here is a basic action that simply logs the entities as JSON:

.. code-block:: php

   <?php

    class LogJsonAction implements ActionInterface
    {
        protected $logger;

        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }

        public function run(array $entities, array $options)
        {
            foreach ($entities as $entity) {
                $this->logger->info(json_encode($entity));
            }

            $response = new ActionResponse(sprintf('Logged %s items.', count($entities));

            return $response;
        }
    }

Define it as a service, and give it the ``perform_base.action`` tag.

.. code-block:: yaml

    app.action.log_json:
        class: AppBundle\Action\LogJsonAction
        arguments:
            - "@logger"
        tags:
            - { name: perform_base.action, alias: app_log_json }

Use the action
--------------

The action is now available in admin classes:

.. code-block:: php

   <?php

    public function configureActions(ActionConfig $config)
    {
        $config->add('app_log_json');
    }

This action will now appear next to each entity and in the batch actions dropdown.

Customising lables
------------------

Restricting action usage
-------------------------
