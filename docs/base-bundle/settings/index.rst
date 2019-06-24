Settings
========

Web applications often need to change behaviour according to user-configurable settings.

Similar to Symfony *parameters*, settings in Perform are values identified with a unique key.
Their values can be set dynamically and per-user.

Get and set values
------------------

The ``SettingsManagerInterface`` has methods to get and set values of settings:

* ``getValue($key, $default = null)`` - get a value, returning a default value if not found.
* ``getRequiredValue($key)`` - get a value, throwing an exception if not found.
* ``setValue($key, $value)`` - set a value.

Values can be any serializable php type.

You can inject the ``perform_base.settings_manager`` service manually into your services, or with autowiring by typehinting against ``Perform\BaseBundle\Settings\Manager\SettingsManagerInterface``.

.. code-block:: php

    <?php

    use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;

    class SomeService
    {
        protected $settings;

        public function __construct(SettingsManagerInterface $settings)
        {
            $this->settings = $settings;
        }

        public function doStuff()
        {
            $perPage = $this->settings->getValue('items_per_page', 10);

            $this->settings->setValue('items_per_page', 20);

            $language = $this->settings->getRequiredValue('lang');
            // whoops! \Perform\BaseBundle\Exception\SettingNotFoundException thrown
        }
    }

User-scoped values
------------------

.. toctree::
   :maxdepth: 2

   ./data-sources
   ./caching
   ./panels
