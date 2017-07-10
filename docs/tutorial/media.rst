Adding media
============

Giving each bike a picture
--------------------------

We don't have much of a bike gallery without pictures.
Let's add a relationship to the ``File`` entity in the ``PerformMediaBundle``.

First, add an image property and access methods to the bike entity in ``src/AppBundle/Entity/Bike.php``:

.. code-block:: diff

      namespace AppBundle\Entity;

    + use Perform\MediaBundle\Entity\File;


.. code-block:: diff

      protected $description;

    + /**
    +  * @var File
    +  */
    + protected $image;


.. code-block:: diff

    + /**
    +  * @param File $image
    +  *
    +  * @return Bike
    +  */
    + public function setImage(File $image)
    + {
    +     $this->image = $image;
    +
    +     return $this;
    + }
    +
    + /**
    +  * @return File
    +  */
    + public function getImage()
    + {
    +     return $this->image;
    + }

Then add the relationship to ``src/AppBundle/Resources/config/doctrine/Bike.orm.yml``:

.. code-block:: diff

      fields:
          title:
              type: string
          description:
              type: text
    + manyToOne:
    +     image:
    +         targetEntity: Perform\MediaBundle\Entity\File
    +         joinColumn:
    +             nullable: true

Now update the database schema to create the new column in the bike table:

.. code-block:: bash

   ./bin/console doctrine:schema:update --force --dump-sql

Updating the admin
------------------

The ``BikeAdmin`` class needs updated to manage the image property:

.. code-block:: diff

      ])->add('description', [
          'type' => 'text',
    + ])->add('image', [
    +     'type' => 'media',
    +     'options' => [
    +         'types' => 'image',
    +     ],
      ]);

Now head to the bike admin page at http://127.0.0.1:8000/admin/bikes.
You'll notice a new column for the image, and that creating a bike requires you to choose an image from the media library.

Uploading media
---------------

Let's add some images of hip and trendy bikes to the library.
Searching for 'hipster bike' on https://unsplash.com gives some surprisingly good results!

Click on the media tab to show the media library, then click 'upload' to add some images.

.. image:: media_upload.png

You can upload multiple images at once, and any large files will be split into chunks to get around PHP's maximum upload size.

Find out more in the :doc:`media bundle documentation <../bundles/media/index>`.

Enforcing schema correctness
----------------------------

We want to make sure that *every* bike in the gallery has an image.

Check that every bike you've created has an image linked to it, then update the schema again in ``Bike.orm.yml``:

.. code-block:: diff

      manyToOne:
          image:
              targetEntity: Perform\MediaBundle\Entity\File
              joinColumn:
    -             nullable: true
    +             nullable: false

Then update the database schema:

.. note::

   Warning! When using SQLite, constraint violations that occur when running ``doctrine:schema:update`` *will* result in the table being emptied.
   See the :doc:`databases guide <../more/databases>` to learn why this occurs.

   Double check every bike has an image before running this command!

.. code-block:: bash

   ./bin/console doctrine:schema:update --force --dump-sql

This ensures that all bike entities in the database have an image.

We've managed to add this column safely, first by adding it without the constraint (``nullable: true``), populating it with data, then applying the constraint (``nullable: false``).

This stepwise approach is a good way to add a new constraint to your database safely.

.. note::

   In a production scenario, you should use database migrations instead of the schema tool to prevent data loss.

Showing pictures on the frontend
--------------------------------

Go ahead and create some bikes in the admin, linking them to your newly uploaded media files.

Update ``src/AppBundle/Resources/views/home.html.twig`` to show images alongside the bikes:

.. code-block:: diff

      {% block content %}
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              {% for bike in bikes %}
              <h2>{{bike.title}}</h2>
    +         {{perform_file_preview(bike.image, {size: 'small', attr: {class: 'img-responsive'}})}}
              <p>
                {{bike.description | nl2br}}
              </p>
              {% endfor %}
            </div>
          </div>
        </div>
      {% endblock %}


The ``perform_file_preview`` twig function is an easy way to display a preview of a file.
Note that we've requested a ``small`` version of the image, and added the bootstrap class ``img-responsive``.

Again, you can read more about how that works in the :doc:`media bundle documentation <../bundles/media/index>`.

Refresh the home page again to see a list of bikes in the database, with images displayed under the titles.
