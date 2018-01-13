<?php

namespace Perform\NotificationBundle\Renderer;

use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface RendererInterface
{
    /**
     * Get the name of the template to use for the given notification.
     */
    public function getTemplateName($publisherName, Notification $notification);

    /**
     * Render content from a named template, using the given notification and recipient.
     */
    public function renderTemplate($template, Notification $notification, RecipientInterface $recipient);
}
