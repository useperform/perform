<?php

namespace Admin\ContactBundle\SpamChecker;

use Admin\ContactBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

/**
 * SpamCheckerInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface SpamCheckerInterface
{
    /**
     * Check if a message is spam, adding the STATUS_SPAM flag to the
     * message.
     *
     * A form and request may not be supplied if the spam checking is
     * being applied to old messages.
     *
     * A checker may record a spam hit using a logger, database table,
     * etc.
     *
     * @param Message            $message
     * @param FormInterface|null $form
     * @param Request|null       $request
     */
    public function check(Message $message, Form $form = null, Request $request = null);
}
