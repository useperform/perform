<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Renderer\RendererInterface;
use Perform\NotificationBundle\Preference\PreferenceInterface;

/**
 * Send notifications to recipients via email.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class EmailPublisher implements PublisherInterface
{
    protected $mailer;
    protected $renderer;
    protected $prefs;
    protected $defaultFrom;

    public function __construct(\Swift_Mailer $mailer, RendererInterface $renderer, PreferenceInterface $prefs, array $defaultFrom)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->prefs = $prefs;
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
            if (!$this->prefs->wantsNotification($recipient, $notification)) {
                continue;
            }
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
