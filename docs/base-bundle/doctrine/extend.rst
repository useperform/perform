Extending entities
==================

A common scenario when using a collection of bundles like Perform is the need to extend an entity.
You want to use all the logic and tools provided by the bundles, but
still add some of your own functionality on top too;
perhaps a few extra fields or relationships.

A common way bundles provide this functionality is declaring the parent entity as a `mapped superclass`; an abstract entity which you then extend.

However, there may be other times when you want to use the bundle's entity `as is`.
In this case a mapped superclass won't work; they are considered abstract entities to Doctrine and therefore not available.
For example, in one application we may wish to extend the user entity with some extra fields, but in another application we want to use it as is.

Defining extended entities
--------------------------

The base bundle provides an event listener that configures an entity as a mapped superclass at the moment the mapping is loaded.
This allows an entity to be used both as a mapped superclass when required, but also as a standalone entity.

To extend an entity, create an entity class that extends the parent class, and then create a new mapping file that declares any extra fields and relationships.
Then declare the entity extension with the config option ``perform_base:extended_entities``.

For example, to extend Perform's user entity:

.. code-block:: php

    <?php
    // src/AppBundle/Entity/User.php

    namespace AppBundle\Entity;

    use Perform\UserBundle\Entity\User as BaseUser;

    class User extends BaseUser
    {
        protected $birthDate;

        public function setBirthDate(\DateTime $birthDate)
        {
            $this->birthDate = $birthDate;

            return $this;
        }

        public function getBirthDate()
        {
            return $this->birthDate;
        }
    }

.. code-block:: yaml

    # src/AppBundle/Resources/config/doctrine/User.orm.yml

    AppBundle\Entity\User:
        type: entity
        fields:
            birthDate:
                type: date

.. code-block:: yaml

    # app/config/config.yml

    perform_base:
        extended_entities:
            "Perform\UserBundle\Entity\\User": "AppBundle\Entity\User"

This will change ``Perform\UserBundle\Entity\User`` to be a mapped superclass, and ``AppBundle\Entity\User`` to be a concrete entity extending it.

Working with the correct entity
-------------------------------

To write portable code that works for bundle entities as well as any entities that may extend them, we need a way of determining the correct entity to use.

The ``perform_base.doctrine.entity_resolver`` service, an instance of ``Perform\BaseBundle\Doctrine\EntityResolver``, is used to figure out which entity to use.
You should use the ``resolve()`` method of this service when interacting with Doctrine to ensure your code will work if the entity in question is extended.

For example, to get a fetch a user by id, regardless of it being extended or not:

.. code-block:: php

   <?php

   /* @var EntityResolver $resolver */
   /* @var EntityManager $em */
   $repo = $em->getRepository($resolver->resolve('PerformUserBundle:User'));
   $user = $repo->find(1);

``$user`` may be an instance of ``Perform\UserBundle\Entity\User``, or perhaps an instance of ``AppBundle\Entity\User``, but as long as the code expects a ``Perform\UserBundle\Entity\User`` it will continue to work correctly.

For instance, this method will always work, regardless of the user entity being extended or not:

.. code-block:: php

   <?php

   public function botherUser(\Perform\UserBundle\Entity\User $user) {
       //...
   }

EntityResolver usage
--------------------

``EntityResolver`` will always return the fully qualified classname of the concrete entity.

You may pass a classname (``Perform\UserBundle\Entity\User``), an alias (``PerformUserBundle:User``), or an instance of the entity (``new User()``) to ``resolve()``.

In this case, because the entity has been extended, all calls will return ``AppBundle\Entity\User``.

If it had not been extended, all calls would return ``Perform\UserBundle\Entity\User``.

Crud for extended entities
----------------------------

Crud classes for entities that have been extended will work for the child entities, although they won't be aware of the new fields and relationships.

For example, if you extended ``Perform\UserBundle\Entity\User`` with ``AppBundle\Entity\User``, routing to the ``perform_user.user`` crud name would use ``AppBundle\Entity\User`` entity classes, but it won't be aware of the fields on ``AppBundle\Entity\User``.

To define a new crud class for the extended entity, create a new class that extends the existing class and register it as a service:

.. code-block:: php

   <?php

    use Perform\UserBundle\Crud\UserCrud as BaseCrud;
    use Perform\BaseBundle\Config\FieldConfig;

    class UserCrud extends BaseCrud
    {
        public function configureFields(FieldConfig $config)
        {
            parent::configureFields($config);

            $config->add('birthDate', [
                'type' => 'date',
            ]);
        }
    }

.. code-block:: yaml

    AppBundle\Crud\UserCrud:
        tags:
            - {name: perform_base.crud, crud_name: "user"}

.. note::

   Remember that auto-configuration will add a ``perform_base.crud`` tag for you with sensible defaults.
