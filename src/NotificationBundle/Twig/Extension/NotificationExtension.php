<?php

namespace Perform\NotificationBundle\Twig\Extension;

use Perform\NotificationBundle\Repository\NotificationLogRepository;
use Perform\NotificationBundle\RecipientInterface;
use Perform\NotificationBundle\RecipientProvider\RecipientProviderInterface;

/**
 * NotificationExtension.
 **/
class NotificationExtension extends \Twig_Extension
{
    protected $provider;
    protected $repository;

    public function __construct(RecipientProviderInterface $provider, NotificationLogRepository $repository)
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
            new \Twig_SimpleFunction('perform_unread_notification_count', [$this, 'getUnreadNotificationCount']),
        ];
    }

    /**
     * @return int
     */
    public function getUnreadNotificationCount()
    {
        $recipient = $this->provider->getRecipients();
        if (!$recipient instanceof RecipientInterface) {
            return 0;
        }

        return $this->repository->getUnreadCountByRecipient($recipient);
    }
}
