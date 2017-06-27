<?php

namespace Perform\NotificationBundle\Twig\Extension;

use Perform\NotificationBundle\Repository\NotificationLogRepository;
use Perform\NotificationBundle\Recipient\RecipientInterface;
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
            new \Twig_SimpleFunction('perform_notification_unread_count', [$this, 'getUnreadCount']),
        ];
    }

    /**
     * @return int
     */
    public function getUnreadCount()
    {
        $recipients = $this->provider->getRecipients();
        if (!isset($recipients[0]) || !$recipients[0] instanceof RecipientInterface) {
            return 0;
        }

        return $this->repository->getUnreadCountByRecipient($recipients[0]);
    }
}
