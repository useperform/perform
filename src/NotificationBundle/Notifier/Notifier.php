<?php

namespace Perform\NotificationBundle\Notifier;

use Perform\NotificationBundle\Notification;
use Perform\NotificationBundle\Event\SendEvent;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Notifier implements NotifierInterface
{
    protected $publishers = [];
    protected $dispatcher;

    public function __construct(LoopableServiceLocator $publishers, EventDispatcherInterface $dispatcher)
    {
        $this->publishers = $publishers;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Send a notification.
     *
     * @param Notification $notification
     * @param array        $publishers   The publishers to use
     */
    public function send(Notification $notification, array $publishers)
    {
        $event = new SendEvent($notification, $publishers);
        $this->dispatcher->dispatch(SendEvent::PRE_SEND, $event);
        foreach ($publishers as $name) {
            if (!$this->publishers->has($name)) {
                throw new \Exception(sprintf('Unknown notification publisher "%s". Available publishers are "%s". You may need to set configuration to enable additional publishers.', $name, implode('", "', $this->publishers->getNames())));
            }

            $this->publishers->get($name)->send($notification);
        }
        $this->dispatcher->dispatch(SendEvent::POST_SEND, $event);
    }
}
