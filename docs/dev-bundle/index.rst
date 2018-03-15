Dev Bundle
==========

Build applications faster than ever with the PerformDevBundle.

Its recommended to only use this bundle during development, so only add it to your AppKernel in the ``dev`` environment:

.. code-block:: php

   <?php

   class AppKernel extends Kernel
   {
       public function registerBundles()
       {
           //... other bundles

           if (in_array($this->getEnvironment(), ['dev'], true)) {
               $bundles[] = new Perform\DevBundle\PerformDevBundle();
           }

           return $bundles;
       }
   }

.. toctree::
   :maxdepth: 1

   frontends
