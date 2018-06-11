<?php

namespace Perform\NotificationBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\NotificationBundle\Notifier\TraceableNotifier;

/**
 * NotificationsDataCollector.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NotificationsDataCollector extends DataCollector
{
    protected $notifier;

    public function __construct(TraceableNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['sent'] = $this->notifier->getSent();
        $this->data['sentCount'] = count($this->data['sent']);
        $this->data['publishers'] = $this->notifier->getPublisherClasses();
    }

    public function getSent()
    {
        return $this->data['sent'];
    }

    public function getSentCount()
    {
        return $this->data['sentCount'];
    }

    public function getPublishers()
    {
        return $this->data['publishers'];
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getName()
    {
        return 'perform_notification';
    }
}
