<?php

namespace Perform\ContactBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Action\ActionResponse;
use Perform\BaseBundle\Action\ActionInterface;

/**
 * SpamAction.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SpamAction implements ActionInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(array $messages, array $options)
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

    public function getDefaultConfig()
    {
        return [
            'label' => 'Flag as spam',
        ];
    }
}
