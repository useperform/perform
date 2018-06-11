Tagging content
===============

There's often a requirement to group, categorise, or tag different
entities so they can be found easily.

The PerformBaseBundle includes a ``Tag`` entity that can be used for this purpose.

Setup
-----

Add a ``manyToMany`` mapping:

.. code-block:: yaml

    manyToMany:
        tags:
            targetEntity: Perform\BaseBundle\Entity\Tag
            cascade:
                - persist


Then define a ``tags`` property on your entity.
To save time, you can avoid creating getters and setters by using
the ``Perform\BaseBundle\Entity\TaggableTrait`` trait.

.. code-block:: php

    <?php

    use Perform\BaseBundle\Entity\TaggableTrait;
    use Doctrine\Common\Collections\ArrayCollection;

    class Bike
    {
        use TaggableTrait;

        public function __construct()
        {
            $this->tags = new ArrayCollection();
        }
    }

Tag type
--------

With a ``tags`` property mapped and defined, you can use the ``tag`` type
in your admin classes to tag your entities.

.. code-block:: php

    <?php

    public function configureFields(FieldConfig $config)
    {
        $config->add('tags', [
            'type' => 'tag',
            'options' => [
                'discriminator' => 'bike',
            ]
        ]);
    }


The discriminator option is required - an arbitrary string that
separates this entity type from others.
Since all tags are stored in the same database table, there would be
no way to distinguish between tags for different entities without this option.

For example, ``BlogPost`` and ``Bike`` entities should have different
discriminators, so the blog pages only display tags for blog posts, not for
bikes.

Multiple tag columns
--------------------

There's nothing stopping you defining multiple taggable properties on an entity:

.. code-block:: php

    <?php

    public function configureFields(FieldConfig $config)
    {
        $config->add('categories', [
            'type' => 'tag',
            'options' => [
                'discriminator' => 'blog_category',
            ]
        ]);
        $config->add('tags', [
            'type' => 'tag',
            'options' => [
                'discriminator' => 'blog_tag',
            ]
        ]);
    }

Be aware that ``TaggableTrait`` only adds getters and setters for the ``tags`` property.
For anything else you'll have to add them yourself.
