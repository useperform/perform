<?php

namespace Perform\SpamBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotEvent extends Event
{
    const CAUGHT = 'perform_spam.honeypot_caught';

    /**
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * Get the honeypot type that triggered this event.
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get the top level parent form.
     *
     * @return FormInterface
     */
    public function getRootForm()
    {
        $current = $this->form;
        while ($current->getParent() !== null) {
            $current = $current->getParent();
        }

        return $current;
    }
}
