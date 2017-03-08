<?php

namespace Perform\ContactBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Action\ActionResponse;

/**
 * NewAction.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NewAction
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run($message, array $options)
    {
        $message->setStatus(Message::STATUS_NEW);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $response = new ActionResponse('Message marked as new.');
        $response->setRedirectRoute('perform_contact_message_list');

        return $response;
    }

    public function isGranted($message)
    {
        return $message->getStatus() !== Message::STATUS_NEW;
    }

    public function getLabel($message)
    {
        if ($message->getStatus() === Message::STATUS_SPAM) {
            return 'Not spam';
        }

        return 'Mark as new';
    }
}
