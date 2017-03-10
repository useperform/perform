<?php

namespace Perform\ContactBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Action\ActionResponse;

/**
 * SpamAction.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SpamAction
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run($messages, array $options)
    {
        foreach ($messages as $message) {
            $message->setStatus(Message::STATUS_SPAM);
            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();

        $response = new ActionResponse('Message moved to spam.');
        $response->setRedirectRoute('perform_contact_message_list');

        return $response;
    }

    public function isGranted($message)
    {
        return $message->getStatus() !== Message::STATUS_SPAM;
    }

    public function getLabel($message)
    {
        return 'Flag as spam';
    }
}
