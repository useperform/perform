<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\NotificationBundle\Entity\NotificationLog;
use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\NotificationBundle\Renderer\RendererInterface;

/**
 * Local publisher stores notifications in the database for displaying
 * in a notification page.
 */
class LocalPublisher implements PublisherInterface
{
    protected $entityManager;
    protected $renderer;

    public function __construct(ObjectManager $entityManager, RendererInterface $renderer)
    {
        $this->entityManager = $entityManager;
        $this->renderer = $renderer;
    }

    public function send(Notification $notification)
    {
        $template = $this->renderer->getTemplateName('local', $notification);

        foreach ($notification->getRecipients() as $recipient) {
            $log = new NotificationLog();
            $log->setRecipient($recipient);
            $log->setType($notification->getType());
            $log->setContent($this->renderer->renderTemplate($template, $notification, $recipient));
            $this->entityManager->persist($log);
        }

        $this->entityManager->flush();
    }

    public function getName()
    {
        return 'local';
    }
}
