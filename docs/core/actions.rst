Actions
=======

On top of viewing, creating, editing, and deleting entities, you may
want to define operations that act on entities in some way.

For example, an action might:

- archive a message
- trigger a report
- email the selected entities to an administrator

.. note::

    Deleting entities is actually a built-in action. See ``Perform\BaseBundle\Action\DeleteAction``.

Actions are a way of doing these operations without having to create new
controllers, routes, and frontend code every time.

When configured, links to these actions are automatically displayed
next to entities.
Clicking these links will run the action and display a success
message, either on the current page or after redirecting to another
page, depending on the response from the action.

An action can also require confirmation before executing, and can be
configured to only run after passing various conditions.

Create an action
----------------

All actions must implement
``Perform\BaseBundle\Action\ActionInterface``, and define services
tagged with ``perform_base.action``.

``ActionInterface#run()`` should return an instance of
``Perform\BaseBundle\Action\ActionResponse`` or throw an exception.
This method must accept an array of 1 or many
entities, so make sure your action accounts for this.

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

            $response = new ActionResponse(sprintf('Logged %s items.', count($entities)));

            return $response;
        }

        public function isGranted()
        {
            return true;
        }

        public function isAvailable(AdminRequest $request)
        {
            return true;
        }

        public function getDefaultConfig()
        {
            return [
                'label' => 'Write to log',
            ];
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

The action is now available to use in admin classes with the ``configureActions`` method:

.. code-block:: php

   <?php

    public function configureActions(ActionConfig $config)
    {
        // also use the default actions
        parent::configureActions($config);

        // add the custom action
        $config->add('app_log_json');
    }

This action will now appear next to each entity and in the batch actions dropdown.

Choosing where to redirect
--------------------------

You might want to redirect somewhere after running an action.

``ActionResponse`` can have one of the following redirect 'types' attached:

* ``ActionResponse::REDIRECT_NONE`` - don't redirect anywhere (the default)
* ``ActionResponse::REDIRECT_URL`` - redirect to a given url
* ``ActionResponse::REDIRECT_ROUTE`` - redirect to a named route
* ``ActionResponse::REDIRECT_PREVIOUS`` - redirect to the previous page
* ``ActionResponse::REDIRECT_CURRENT`` - reload the current page
* ``ActionResponse::REDIRECT_ENTITY_DEFAULT`` - redirect to the default route for the current entity type (usually the list context)

Set this redirect by calling ``setRedirect()`` on the response before returning it:

.. code-block:: php

   <?php

   $response = new ActionResponse('Success');
   //redirect to the previous page
   $response->setRedirect(ActionResponse::REDIRECT_PREVIOUS);

   //url requires the url option
   $response->setRedirect(ActionResponse::REDIRECT_URL, ['url' => 'https://example.com']);

   //route requires the route and params
   $response->setRedirect(ActionResponse::REDIRECT_URL, ['route' => 'admin_foo_list']);
   $response->setRedirect(ActionResponse::REDIRECT_URL, ['route' => 'admin_foo_view', 'params' => ['id' => 1]]);

.. note::

    ``REDIRECT_URL`` requires the ``url`` option, and ``REDIRECT_ROUTE`` requires the ``route`` and ``params`` options.


Requiring confirmation
----------------------

If your action is potentially destructive (e.g. deleting data), you
might want to require confirmation to prevent accidental data loss.

Setting the ``confirmationRequired`` option to ``true`` will require
the action to be confirmed before proceeding.

.. code-block:: php

   <?php

    class DeleteAction implements ActionInterface
    {
        //...

        public function getDefaultConfig()
        {
            return [
                'confirmationRequired' => true,
            ];
        }
    }

A confirmation modal window will now appear when selecting this action.

Like all other options, this can be overridden when adding the action in an entity admin:

.. code-block:: php

   <?php

    public function configureActions(ActionConfig $config)
    {
        // no confirmation required for deletes in the wild west
        $config->add('delete', [
            'confirmationRequired' => false
        ]);
    }

Customising labels
------------------

The values of the ``label`` and ``batchLabel`` options will be used to label
action buttons and the value in the batch actions dropdown.

.. code-block:: php

   <?php

    class DeleteAction implements ActionInterface
    {
        //...

        public function getDefaultConfig()
        {
            return [
                'label' => 'Delete',
                'batchLabel' => 'Delete these things',
            ];
        }
    }

``label`` will default to something reasonable if not defined.
``batchLabel`` will default to ``label`` if not defined.

Labels can also be overridden when adding the action in an entity admin:

.. code-block:: php

   <?php

    public function configureActions(ActionConfig $config)
    {
        $config->add('delete', [
            'label' => 'Destroy',
            'batchLabel' => 'Destroy these things',
        ]);
    }

Both options can also be a function, allowing for dynamic labels.
They are passed the current instance of
``Perform\BaseBundle\Admin\AdminRequest``, and the ``label`` function
will be passed the entity in question.

.. code-block:: php

   <?php

    public function configureActions(ActionConfig $config)
    {
        $config->add('delete', [
            'label' => function($request, $entity) {
                return sprintf('Remove %s', $entity->getId());
            },
        ]);
    }

Restricting usage
-----------------

Use ``isGranted`` to restrict an action to certain conditions:

.. code-block:: php

   <?php

   public function isGranted($entity)
   {
        // only allow this action on non-archived entities
        return !$entity->isArchived();
   }

Use ``isAvailable`` to restrict when to display a batch action option.

.. code-block:: php

   <?php

   public function isAvailable(AdminRequest $request)
   {
        // don't show the batch action when viewing the 'archived' filter
        return $request->getFilter() !== 'archived';

        // something wacky - only show the batch action on the 2nd page
        return $request->getPage() === 2;
   }

.. note::
   ``isAvailable`` should not be used to enforce action permissions.
   It is only called when displaying a batch action option, not when
   actually running an action.

Running actions in the cli
--------------------------

Any registered action can also be executed in the command line with ``perform:action:run``.
It requires the action name, the entity class, and the id of the entity.

.. code-block:: sh

   $ ./bin/console perform:action:run perform_base_delete PerformBlogBundle:Post 88089473-0953-11e7-bb3f-080027ba0e69

   Item deleted.


Multiple ids can also be specified, separated with a space.

.. code-block:: sh

   $ ./bin/console perform:action:run perform_base_delete PerformBlogBundle:Post 88089473-0953-11e7-bb3f-080027ba0e69 8809ccda-0953-11e7-bb3f-080027ba0e69 880aee0f-0953-11e7-bb3f-080027ba0e69

   3 items deleted.

Finally, ``perform:debug:actions`` will show all available actions.
