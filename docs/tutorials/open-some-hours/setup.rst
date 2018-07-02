Setup
=====

We'll start with an empty Symfony flex application and add some Perform bundles on top.

Create a new application using composer:

.. code-block:: bash

    composer create-project symfony/skeleton open-some-hours

This will create the directory ``open-some-hours`` with a brand new Symfony application inside.

Adding the Perform bundles
--------------------------

Since we're using symfony flex, we can simply run ``composer require`` to add the required bundles to our application.

Start with the base bundle:

.. code-block:: bash

   cd open-some-hours/
   composer require perform/base-bundle

This will add the perform base bundle to our application.

Dev dependencies
~~~~~~~~~~~~~~~~

For our convenience, let's add the perform dev bundle, symfony debug tools, and symfony web server:

.. code-block:: bash

   composer require --dev perform/dev-bundle debug-pack server


The first controller
--------------------

Let's create a simple page based on Perform's provided UI.

Create a ``PageController`` class in the file ``src/Controller/PageController.php``:

.. code-block:: php

    <?php

    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

    class PageController
    {
        /**
         * @Route("/")
         * @Template()
         */
        public function index()
        {
            return [];
        }
    }


And a template extending Perform's base template:

.. code-block:: html+twig

    {% extends '@PerformBase/base.html.twig' %}

    {% block page_title %}
      Open Some Hours
    {% endblock %}

    {% block workspace %}
      <h1>Open Some Hours</h1>
    {% endblock %}


Then start the symfony server with the ``server:start`` command:

.. code-block:: bash

   ./bin/console server:start


Now visit http://localhost:8000. We should be greeted with... an unstyled page.

Assets
------

The UI requires scss, javascript, and vue component files in the base bundle to work properly.
These source files need to be compiled before they can work in the browser.

Fortunately, the dev bundle has commands to set this up for us.

Run the ``perform-dev:create:asset-config`` command:

.. code-block:: bash

   ./bin/console perform-dev:create:asset-config

which will generate two files:

* ``package.json`` - to declare the npm modules this app requires
* ``webpack.config.js`` - to tell webpack how to build the assets

These files can be customized to suit the needs of your application, but we'll keep them as they are for this tutorial.

If you open ``package.json``, you'll notice ``dependencies`` list is empty:

.. code-block:: json

    {
      "dependencies": {
      },
      "devDependencies": {
        ...
      }
    }

Run the ``perform-dev:merge-npm-packages`` to add them:

.. code-block:: bash

   ./bin/console perform-dev:merge-npm-packages


This will find the dependencies required by the different Perform bundles (just the base bundle in our case), and add them to ``package.json``.
You'll see them in the ``dependencies`` list now:

.. code-block:: json

    {
      "dependencies": {
        "bootstrap": "...",
        "bootstrap-vue": "...",
        ...
      },
      "devDependencies": {
        ...
      }
    }


We're ready to build the assets now.
Install the requirements with ``npm`` or ``yarn``, then tell webpack to build the assets.

.. code-block:: bash

   npm install # or 'yarn'
   npm run build

The resulting assets files will be placed in the ``public/`` directory.

Refresh the page http://localhost:8000. Hooray, it works!

.. note::

   Building and managing assets is a deep topic, and might seem confusing at first.
   For the sake of getting started, we've only skimmed the surface for this tutorial.

   As you get to know Perform more, the assets workflow will start to make more sense.

   See :doc:`../../base-bundle/assets/index` to find out more.

Save your work
--------------

   At this point in the tutorial, you might want to commit your files to version control.

   Avoid committing the following files and directories:

   * ``vendor/`` - composer packages, commit ``composer.lock`` instead
   * ``node_modules/`` - npm packages, commit ``package-lock.json`` (or ``yarn.lock``) instead
   * ``asset-paths.js``
   * ``public/fonts``, ``public/*.js``, ``public/*.css``, ``public/*.map`` - built asset files

   Instead, make sure they are present in the VCS 'ignore' file, e.g. ``.gitignore``.
