Form types
==========

Honeypot
--------

The ``HoneypotType`` can be used to trap spam bots in a `honeypot
<https://en.wikipedia.org/wiki/Honeypot_(computing)>`_ by adding an
input element that is not supposed to be filled out.

It renders a basic text input, hidden from humans with css.
When a bot attempts to scrape and submit a form, it will often attempt to complete every field.
By looking for any text added to this hidden input, we can detect the
presence of a primitive form filler bot.

.. code-block:: php

    <?php

    use Perform\SpamBundle\Form\Type\HoneypotType;

    class MyFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('rating', HoneypotType::class);
        }
    }


.. note::

   Using a 'normal' looking name for the honeypot field (e.g. 'rating') might confuse bots more than a field called 'honeypot'.


The honeypot type will dispatch a ``HoneypotEvent::CAUGHT`` event when
it detects data in the field.

The ``HoneypotChecker`` listens to this event to mark incoming forms as spam.

Then use the ``SpamManager`` to check if the form submission was spam:

.. code-block:: php

    <?php

    public function someAction(Request $request, SpamManager $manager)
    {
        $form = $this->createForm(MyFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $manager->checkForm($form);
            if ($result->isSpam()) {
                // go to an error page, redirect, block user, etc
                return new Response('Go away');
            }
            // handle valid submission
        }

Preventing the form submission
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can also prevent the form from passing validation by setting the ``prevent_submission`` option to ``true``.

A form error will be added when text is detected in the field.

.. code-block:: php

    <?php

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rating', HoneypotType::class, [
            'prevent_submission' => true,
        ]);
    }
