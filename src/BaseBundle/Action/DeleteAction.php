<?php

namespace Perform\BaseBundle\Action;

use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Crud\EntityManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DeleteAction implements ActionInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(array $entities, array $options)
    {
        $this->entityManager->deleteMany($entities);

        $response = new ActionResponse(sprintf('%s deleted.', count($entities) === 1 ? 'Item' : count($entities).' items'));
        $response->setRedirect(
            isset($options['context']) && $options['context'] === CrudRequest::CONTEXT_VIEW ?
            ActionResponse::REDIRECT_PREVIOUS :
            ActionResponse::REDIRECT_CURRENT
        );

        return $response;
    }

    public function getDefaultConfig()
    {
        return [
            'label' => 'Delete',
            'confirmationRequired' => true,
            'buttonStyle' => 'btn-danger',
            'isGranted' => function ($entity, $authChecker) {
                return $authChecker->isGranted('DELETE', $entity);
            },
        ];
    }
}
