Built-in forms
==============

To save time, the MailingListBundle includes two built in form types,
``EmailOnlyType`` and ``EmailAndNameType``.

Use them to create new subscribers without having to create your own forms each time.

Multiple forms on the same page
-------------------------------

Symfony's form component doesn't allow the same form type to be
created twice; they will be considered to be the same form.
This is a problem if we want to use multiple forms on the same page.

For example, we might have a newsletter form at the bottom of every
page, but *also* have a beta list signup page that includes a form
too.
It'd be great if both of these could use the ``EmailOnlyType``.

Use the ``create`` method of the ``perform_mailing_list.form_factory``
service to create multiple forms of the same type that submit to
different urls.
They will be given different names, so Symfony will be aware of the difference.

.. code-block:: php

   <?php

   /** @var Perform\MailingListBundle\Form\UniqueFormFactory $factory */
   $factory = $container->get('perform_mailing_list.form_factory');

   $newsletterForm = $factory->create('email_only', '/mailing-lists/newsletter');
   $betaForm = $factory->create('email_only', '/mailing-lists/beta');


You can also use the ``perform_mailing_list_form`` twig helper to get
an instance of these forms in your templates:

.. code-block:: html+twig

    <h4>Subscribe to our newsletter!</h4>
    {% set form = perform_mailing_list_form('email_only', '/mailing-lists/newsletter') %}
    {{form_start(form)}}
    {{form_row(form.email}}
    <input name="signup" type="submit" class="btn btn-primary" value="Subscribe" />
    {{form_end(form)}}
