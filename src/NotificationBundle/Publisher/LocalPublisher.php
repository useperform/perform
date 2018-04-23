<?php

namespace Perform\NotificationBundle\Publisher;

use Perform\NotificationBundle\Entity\NotificationLog;
use Perform\NotificationBundle\Notification;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\NotificationBundle\Renderer\RendererInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Local publisher saves notifications in the database for users to
 * view. Therefore, all recipients must implement Symfony's
 * UserInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
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
            if (!$recipient instanceof UserInterface) {
                continue;
            }

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
