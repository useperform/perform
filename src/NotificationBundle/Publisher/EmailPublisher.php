<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\BaseBundle\Email\Mailer;
use Perform\NotificationBundle\Notification;
use Symfony\Component\Templating\EngineInterface;
use Perform\NotificationBundle\Renderer\RendererInterface;

/**
 * EmailPublisher sends the notifications to the recipients via email.
 */
class EmailPublisher implements PublisherInterface
{
    protected $mailer;
    protected $renderer;

    public function __construct(Mailer $mailer, RendererInterface $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function send(Notification $notification)
    {
        $template = $this->renderer->getTemplateName('email', $notification);
        $context = $notification->getContext();

        if (!isset($context['subject'])) {
            throw new \InvalidArgumentException(__CLASS__.' requires the "subject" property in the notification context.');
        }

        foreach ($notification->getRecipients() as $recipient) {
            //add useful context to the template
            $context = array_merge($notification->getContext(), [
                'notification' => $notification,
                //all recipients are available in the template with
                //notification.recipients, this is just the current
                //recipient
                'currentRecipient' => $recipient,
            ]);

            $message = $this->mailer->createMessage(
                $recipient->getEmail(),
                $context['subject'],
                $template,
                $context
            );
            if (isset($context['replyTo'])) {
                $message->setReplyTo((array) $context['replyTo']);
            }

            $this->mailer->sendMessage($message);
        }
    }

    public function getName()
    {
        return 'email';
    }
}
