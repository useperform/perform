<?php

namespace Perform\ContactBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

/**
 * HoneypotEvent.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotEvent extends Event
{
    const CAUGHT = 'perform_honeypot_caught';

    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getRootForm()
    {
        $current = $this->form;
        while ($current->getParent() !== null) {
            $current = $current->getParent();
        }

        return $current;
    }
}
