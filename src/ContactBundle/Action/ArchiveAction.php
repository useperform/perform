<?php

namespace Perform\ContactBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Action\ActionResponse;

/**
 * ArchiveAction
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ArchiveAction
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(array $messages, array $options)
    {
        foreach ($messages as $message) {
            $message->setStatus(Message::STATUS_ARCHIVE);
            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();

        $response = new ActionResponse('Message archived.');
        $response->setRedirectRoute('perform_contact_message_list');

        return $response;
    }

    public function isGranted($message)
    {
        return $message->getStatus() === Message::STATUS_NEW;
    }

    public function getLabel($message)
    {
        return 'Archive';
    }
}
