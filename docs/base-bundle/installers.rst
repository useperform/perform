Installers
==========

Complex applications often require additional setup steps when they are deployed, such as:

* Seeding a database with initial data
* Building search indexes
* Populating a network cache
* Running some other heavy computation

Instead of adding more and more commands to the deployment process:

.. code-block:: bash

   ./bin/console app:seed-database
   ./bin/console app:build-search-index
   ./bin/console some-bundle:distribute-changes


we can run a single command instead:

.. code-block:: bash

   $ ./bin/console perform:install

   Running App\Installer\DBInstaller
   Running App\Installer\SearchInstaller
   Running SomeBundle\Installer\ChangeSetsInstaller

The perform bundles include some installers of their own.
For example, the ``Perform\UserBundle\Installer\UsersInstaller`` creates initial users in the database by reading from its bundle configuration.

Creating an installer
---------------------

To create an installer, write a service that implements ``Perform\BaseBundle\Installer\InstallerInterface`` and have the ``perform_base.installer`` container tag.

.. note::

   If you're using service auto-configuration, all services implementing ``InstallerInterface`` will be given the tag automatically.

The interface has only one method, ``install()``.
The only argument is a logger, which you can use to show the progress of the installation process.

Here's an example using a fictional ``DataLoader`` to populate address information:

.. code-block:: php

    use Psr\Log\LoggerInterface;
    use Perform\BaseBundle\Installer\InstallerInterface;
    use App\Address\DataLoader;

    class AddressInfoInstaller implements InstallerInterface
    {
        protected $loader;

        public function __construct(DataLoader $loader)
        {
            $this->loader = $loader;
        }
        public function install(LoggerInterface $logger)
        {
            if ($this->loader->alreadyImported('addresses.csv')) {
                return;
            }

            $logger->info('Importing address lookup information into the database from <info>addresses.csv</info>');
            $this->loader->load('addresses.csv');
        }
    }

.. note::

    Notice that the example installer is idempotent - it can be run many times, but the data will only be added once.

    You will typically want to run the ``perform:install`` console command on every deployment, so make sure your installers account for this.

Installers are not fixtures
---------------------------

Seeding a database is not the same as database *fixtures*.
Fixtures are usually randomized dummy data used for testing, not for production environments.

In contrast to this, initial database data is required in production environments for applications to function correctly.
A good example would be address lookup information for a checkout form.

An installer is a good choice for creating this initial data, but fixtures should be run using their dedicated console commands.
