Licensing
=========

Perform is commercial software. You need a license to use the code in a production environment.

Each of your applications must be configured with a *project key*, a key linked to a *project* you register on the Perform website.

You can create one project for each of your single licenses, or unlimited projects with a valid pro or bespoke license.

Deploying a project
-------------------

To get a key, create a new project in your `account page </account>`_, specifying the domains you'll use for your application.
Each project will have a different project key.

.. image:: ./account_project.png

For deployment, set the ``perform.project_key`` parameter to the value of this key:

.. code-block:: yaml

   # app/config/parameters.yml

   perform.project_key: p-00000000000000000000000000000000

In a *non-debug* kernel environment, the project key will be validated with Perform's licensing server every time the cache is cleared.
If the project key is invalid, or the domain isn't in the list of valid domains, the application will show an error page.

License types and expiration
----------------------------

**Single**: a one-off purchase. Each single license can create one project.

**Pro and Bespoke**: a yearly subscription. You can create unlimited projects as long as you pay for the subscription.

Projects are valid forever, even if they were created with a pro or bespoke license that has now expired.
