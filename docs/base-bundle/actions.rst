Actions
=======

On top of viewing, creating, editing, and deleting entities, you may
want to define operations that act on entities in some way.

For example, you might want operations to:

- archive a message
- trigger a report
- email the selected entities to an administrator

.. note::

    Deleting entities is actually a built-in action. See ``Perform\BaseBundle\Action\DeleteAction``.

Actions are a way of doing these operations without having to create new
controllers, routes, and frontend code every time.

When configured, buttons for these actions are automatically displayed
next to entities.
Clicking an action button will send an AJAX request to run the action
and then display a success message, either on the current page or
after redirecting to another page, depending on the response from the
action.

An action can also require confirmation before executing, and can be
configured to only run after passing various conditions.

Required configuration
----------------------

Add ``routing_action.yml`` from the `BaseBundle` to your routing configuration:

.. code-block:: yaml

    perform_base_action:
        resource: "@PerformBaseBundle/Resources/config/routing_action.yml"
        prefix: /admin/_action

This resource loads routes for a controller in the `BaseBundle` that handles the different actions.
Javascript code on the frontend sends POST requests to this controller when action buttons are clicked.

Make sure to use a sensible prefix that won't conflict with any existing routes, e.g. ``/admin/_action``.

Using actions
-------------

Use the ``addInstance`` and ``add`` methods of the ``ActionConfig`` object in ``Perform\BaseBundle\Crud\CrudInterface#configureActions()`` to add actions for your entity.

``addInstance`` requires an arbitrary action name (unique for this entity only) and an instance of ``Perform\BaseBundle\Action\ActionInterface``.
However, you'll often find that actions require injected dependencies, such as the Doctrine entity manager to save entities.

Fortunately, actions can be added to a `registry`, which handles their dependencies and allows you to refer to them globally by name.
If the desired action is in the registry, use ``add``, which only requires the action name.

.. note::

   Use the ``perform:debug:actions`` console command to view all registered actions in the registry.

Both ``addInstance`` and ``add`` take an array of options which can be used to customize how the button is displayed and how it behaves.

.. code-block:: php

   <?php

    public function configureActions(ActionConfig $config)
    {
        // add a registered action with some options
        $config->add('perform_base_delete', [
            'label' => 'Destroy',
            'confirmationRequired' => true,
        ]);

        // or add an action instance (assuming $this->em is an injected Doctrine entity manager)
        $config->addInstance('delete', new DeleteAction($this->em));
    }

Creating a new action
---------------------

To create your own action, implement ``Perform\BaseBundle\Action\ActionInterface``.
To register it with the action registry, define it as a service
with the ``perform_base.action`` tag.

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

The new action is now available to use in the ``configureActions`` method:

.. code-block:: php

   <?php

    public function configureActions(ActionConfig $config)
    {
        // also use the default actions
        parent::configureActions($config);

        // add the custom action
        $config->add('app_log_json');

        // or as an instance, if you skipped creating a service for it.
        // note that the logger will have to be injected to your admin class.
        $config->addInstance('log', new LogJsonAction($this->logger));
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

   //route requires the route name and params
   $response->setRedirect(ActionResponse::REDIRECT_ROUTE, ['route' => 'crud_foo_list']);
   $response->setRedirect(ActionResponse::REDIRECT_ROUTE, ['route' => 'crud_foo_view', 'params' => ['id' => 1]]);

   //default route of the current entity (usually the list context)
   $response->setRedirect(ActionResponse::REDIRECT_ENTITY_DEFAULT);

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
``Perform\BaseBundle\Crud\CrudRequest``, and the ``label`` function
will also be passed the entity in question.

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

Use the ``isGranted`` option to restrict an action to certain conditions:


.. code-block:: php

    <?php

    public function getDefaultConfig()
    {
        return [
            'label' => 'Archive',
            'isGranted' => function($message, AuthorizationCheckerInterface $authChecker) {
                // only allow this action on non-archived entities, and if the user is allowed to
                return $message->getStatus() !== Message::STATUS_ARCHIVED && $authChecker->isGranted('ARCHIVE', $message);
            },
        ];
    }

This option can either be a boolean or a function that returns a boolean.
If a function, it is called with the entity in question and an
instance of
``Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface``,
which you can use to query the Symfony security system.
If it evaluates to ``true``, the button will be displayed next to the entity.

The default is ``true``.

Deciding when to show the buttons
---------------------------------

Use the ``isButtonAvailable`` and ``isBatchOptionAvailable`` options to decide when to show action buttons.

``isButtonAvailable`` decides when to show a button next to an entity.

The value can be a boolean or a function that returns a boolean.
If a function, it is called with the entity in question and an ``CrudRequest`` instance.

The default is ``true``.

.. code-block:: php

    <?php

    public function getDefaultConfig()
    {
        return [
            'label' => 'Archive',
            'isButtonAvailable' => function($message, CrudRequest $request) {
                return $message->getStatus() !== Message::STATUS_ARCHIVED;
            }
        ];
    }

.. note::
   The result of ``isGranted`` is also used when deciding to display a
   button, since it doesn't make sense to display a button for an
   action that is not allowed.
   If ``isButtonAvailable`` evaluates to ``true`` but ``isGranted``
   does not, the button will not be shown.


``isBatchActionAvailable`` decides when to display a batch action option.

This can also be a boolean or a function that returns a boolean.
If a function, it is passed an ``CrudRequest`` instance.

The default is ``true``.

.. code-block:: php

    <?php

    public function getDefaultConfig()
    {
        return [
            'label' => 'Archive',
            'isBatchOptionAvailable' => function(CrudRequest $request) {
                // don't show the batch action when viewing the 'archived' filter
                return $request->getFilter() !== 'archived';

                // or something wacky - only show the batch action on the 2nd page
                return $request->getPage() === 2;
            }
        ];
    }

Handling errors
---------------

Any exceptions that are thrown during an action's ``run`` method will
result in a generic error message shown to the user.

You can also specify the error message to show by throwing a
``Perform\BaseBundle\Action\ActionFailedException``.

.. code-block:: php

    <?php
    public function run(array $entities, array $options)
    {
        // will show a generic error shown to the user, hiding exception details
        throw new \RuntimeException('The flux capacitor failed to start.');

        // will show the exception message to the user
        throw new ActionFailedException('The flux capacitor failed to start.');
    }

Link actions
------------

You can also use the ``configureActions`` method to add simple links
to other resources, but with the additional benefits that actions
bring, such as requiring confirmation.

To add links next to entities, use the ``addLink`` method of ``ActionConfig``:

.. code-block:: php

   <?php

   public function configureActions(ActionConfig $config)
   {
       parent::configureActions($config);
       $config->addLink(
           function($user) {
               return '/?_switch_user='.$user->getEmail();
           },
           'Impersonate user',
           [
               'confirmationRequired' => true,
           ]
       );
   }

``addLink`` has two required parameters: the link and the label, both
of which can be either a string or a function that returns a string
depending on the entity.
In the above example, the link parameter is a function that changes
depending on the user's email address.

If the link parameter is a function, it will be passed the entity in
question, plus an instance of
``Perform\BaseBundle\Routing\CrudUrlGeneratorInterface`` and
``Symfony\Component\Routing\Generator\UrlGeneratorInterface`` as the
second and third arguments, to make it easy to create a URL.

``addLink`` optionally takes an array of options as a third parameter,
where all of the options of ``add`` and ``addInstance`` can also be
used.

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

Use the ``perform:debug:actions`` console command to show all the available actions.
