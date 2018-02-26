<?php

namespace Perform\ContactBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Action\ActionResponse;
use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Admin\AdminRequest;

/**
 * Mark a message as new.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NewAction implements ActionInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(array $messages, array $options)
    {
        foreach ($messages as $message) {
            $message->setStatus(Message::STATUS_NEW);
            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();

        $response = new ActionResponse(sprintf('%s marked as new.', count($messages) === 1 ? 'Message' : count($messages).' messages'));
        $response->setRedirectRoute('perform_contact_message_list');

        return $response;
    }

    public function getDefaultConfig()
    {
        return [
            'label' => function($request, $message) {
                if ($message->getStatus() === Message::STATUS_SPAM) {
                    return 'Not spam';
                }

                return 'Mark as new';
            },
            'batchLabel' => function($request) {
                if ($request->getFilter('new') === 'spam') {
                    return 'Not spam';
                }

                return 'Mark as new';
            },
            'isGranted' => function($message) {
                return $message->getStatus() !== Message::STATUS_NEW;
            },
            'isBatchOptionAvailable' => function($request) {
                return $request->getFilter('new') !== 'new';
            }
        ];
    }
}
