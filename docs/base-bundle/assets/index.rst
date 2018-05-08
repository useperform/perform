Assets
======

The interface depends on various scss styles, themes, vue components,
and other javascript files.
These assets come from many bundles (not just the base bundle) and
need to be built with a module bundler such as webpack.

Each application is responsible for finding the assets provided by each bundle, combining them together, and generating optimized css and javascript files.

Fortunately, you don't have to create this build pipeline yourself (unless you want to).
Perform comes with tools to create it for you.

.. toctree::
   :maxdepth: 2

   ./building
   ./adding-new
   ./themes
   ./in-depth
   ./css-namespacing
