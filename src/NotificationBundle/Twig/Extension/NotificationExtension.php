<?php

namespace Admin\NotificationBundle\Twig\Extension;

use Admin\NotificationBundle\Repository\NotificationLogRepository;
use Admin\NotificationBundle\RecipientInterface;
use Admin\NotificationBundle\RecipientProvider\ActiveRecipientProviderInterface;

/**
 * NotificationExtension.
 **/
class NotificationExtension extends \Twig_Extension
{
    protected $provider;
    protected $repository;

    public function __construct(ActiveRecipientProviderInterface $provider, NotificationLogRepository $repository)
    {
        $this->provider = $provider;
        $this->repository = $repository;
    }

    public function getName()
    {
        return 'notification';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('unread_notification_count', [$this, 'getUnreadNotificationCount']),
        ];
    }

    /**
     * @return int
     */
    public function getUnreadNotificationCount()
    {
        $recipient = $this->provider->getActiveRecipient();
        if (!$recipient instanceof RecipientInterface) {
            return 0;
        }

        return $this->repository->getUnreadCountByRecipient($recipient);
    }
}
