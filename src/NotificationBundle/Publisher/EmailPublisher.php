<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Renderer\RendererInterface;

/**
 * Send notifications to recipients via email.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class EmailPublisher implements PublisherInterface
{
    protected $mailer;
    protected $renderer;
    protected $defaultFrom;

    public function __construct(\Swift_Mailer $mailer, RendererInterface $renderer, array $defaultFrom)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->defaultFrom = $defaultFrom;
    }

    public function send(Notification $notification)
    {
        $template = $this->renderer->getTemplateName('email', $notification);
        $context = $notification->getContext();

        if (!isset($context['subject'])) {
            throw new \InvalidArgumentException(__CLASS__.' requires the "subject" property in the notification context.');
        }

        foreach ($notification->getRecipients() as $recipient) {
            $message = (new \Swift_Message)
                     ->setSubject($context['subject'])
                     ->setTo($recipient->getEmail())
                     ->setFrom($this->defaultFrom)
                     ->setBody($this->renderer->renderTemplate($template, $notification, $recipient));

            if (isset($context['replyTo'])) {
                $message->setReplyTo((array) $context['replyTo']);
            }

            $this->mailer->send($message);
        }
    }
}
