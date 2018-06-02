<?php

namespace Perform\ContactBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Action\ActionResponse;
use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * Archive a message.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ArchiveAction implements ActionInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(CrudRequest $crudRequest, array $messages, array $options)
    {
        foreach ($messages as $message) {
            $message->setStatus(Message::STATUS_ARCHIVE);
            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();

        $response = new ActionResponse(sprintf('%s archived.', count($messages) === 1 ? 'Message' : count($messages).' messages'));
        $response->setRedirect(ActionResponse::REDIRECT_LIST_CONTEXT);

        return $response;
    }

    public function getDefaultConfig()
    {
        return [
            'label' => 'Archive',
            'isGranted' => function(Message $message) {
                return $message->getStatus() === Message::STATUS_NEW;
            },
            'isBatchOptionAvailable' => function(CrudRequest $request) {
                return $request->getFilter('new') === 'new';
            }
        ];
    }
}
