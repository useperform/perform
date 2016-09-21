<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\Base\Email\Mailer;
use Perform\NotificationBundle\Notification;
use Symfony\Component\Templating\EngineInterface;

/**
 * EmailPublisher sends the notifications to the recipients via email.
 */
class EmailPublisher implements PublisherInterface
{
    protected $mailer;
    protected $templating;

    public function __construct(Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function send(Notification $notification)
    {
        $type = $notification->getType();
        $pieces = explode(':', $type, 2);
        $template = count($pieces) === 2 ?
                  $pieces[0].':notifications:'.$pieces[1].'/email.html.twig' :
                  "PerformNotificationBundle:$type:email.html.twig";
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

            $this->mailer->send(
                $recipient->getEmail(),
                $context['subject'],
                $template,
                $context
            );
        }
    }

    public function getName()
    {
        return 'email';
    }
}
