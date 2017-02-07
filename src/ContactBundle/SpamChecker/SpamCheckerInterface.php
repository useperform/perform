<?php

namespace Perform\ContactBundle\SpamChecker;

use Perform\ContactBundle\Entity\Message;
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
     * A checker may record a spam hit using a logger, database table,
     * etc.
     *
     * If the doctrine entity manager is used to persist new entities,
     * there is no need to call flush().
     *
     * @param Message       $message
     * @param FormInterface $form
     * @param Request       $request
     */
    public function check(Message $message, FormInterface $form, Request $request);
}
