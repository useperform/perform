<?php

namespace Perform\ContactBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Action\ActionResponse;
use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * Mark a message as spam.
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

    public function run(CrudRequest $crudRequest, array $messages, array $options)
    {
        foreach ($messages as $message) {
            $message->setStatus(Message::STATUS_SPAM);
            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();

        $response = new ActionResponse(sprintf('%s moved to spam.', count($messages) === 1 ? 'Message' : count($messages).' messages'));
        $response->setRedirect(ActionResponse::REDIRECT_LIST_CONTEXT);

        return $response;
    }

    public function getDefaultConfig()
    {
        return [
            'label' => 'Flag as spam',
            'isGranted' => function(Message $message) {
                return $message->getStatus() !== Message::STATUS_SPAM;
            },
            'isBatchOptionAvailable' => function(CrudRequest $request) {
                return $request->getFilter('new') !== 'spam';
            }
        ];
    }
}
