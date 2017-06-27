<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\NotificationBundle\Entity\NotificationLog;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Templating\EngineInterface;

/**
 * Local publisher stores notifications in the database for displaying
 * in a notification page. Templates are used to customise what each
 * notification type looks like.
 */
class LocalPublisher implements PublisherInterface
{
    protected $entityManager;
    protected $templating;

    public function __construct(ObjectManager $entityManager, EngineInterface $templating)
    {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
    }

    public function send(Notification $notification)
    {
        $type = $notification->getType();
        $pieces = explode(':', $type, 2);
        $template = count($pieces) === 2 ?
                  $pieces[0].':notifications:'.$pieces[1].'/local.html.twig' :
                  "PerformNotificationBundle:$type:local.html.twig";

        foreach ($notification->getRecipients() as $recipient) {
            $log = new NotificationLog();
            $log->setRecipient($recipient);
            $log->setType($type);
            $log->setContent($this->renderTemplate($template, $notification, $recipient));
            $this->entityManager->persist($log);
        }

        $this->entityManager->flush();
    }

    protected function renderTemplate($template, Notification $notification, RecipientInterface $recipient)
    {
        //add useful context to the template
        $context = array_merge($notification->getContext(), [
            'notification' => $notification,
            //all recipients are available in the template with
            //notification.recipients, this is just the current
            //recipient
            'currentRecipient' => $recipient,
        ]);

        return $this->templating->render($template, $context);
    }

    public function getName()
    {
        return 'local';
    }
}
