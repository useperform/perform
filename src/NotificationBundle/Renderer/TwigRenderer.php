<?php

namespace Perform\NotificationBundle\Renderer;

use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TwigRenderer implements RendererInterface
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getTemplateName($publisherName, Notification $notification)
    {
        $type = $notification->getType();
        $pieces = explode(':', $type, 2);
        if (count($pieces) !== 2) {
            return sprintf('notification/%s/%s.html.twig', $type, $publisherName);
        }

        $namespace = preg_replace('/Bundle$/', '', $pieces[0]);

        return sprintf('@%s/notification/%s/%s.html.twig', $namespace, $pieces[1], $publisherName);
    }

    public function renderTemplate($template, Notification $notification, RecipientInterface $recipient)
    {
        //add useful context to the template
        $context = array_merge($notification->getContext(), [
            'notification' => $notification,
            //all recipients are available in the template with
            //notification.recipients, this is just the current
            //recipient
            'currentRecipient' => $recipient,
        ]);

        return $this->twig->render($template, $context);
    }
}
