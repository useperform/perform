<?php

namespace Perform\BaseBundle\Action;

use Doctrine\ORM\EntityManager;
use Perform\BaseBundle\Admin\AdminRequest;

/**
 * DeleteAction.
 *
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
        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();

        $response = new ActionResponse(sprintf('%s deleted.', count($entities) === 1 ? 'Item' : count($entities).' items'));
        $response->setRedirect(ActionResponse::REDIRECT_CURRENT);

        return $response;
    }

    public function isGranted($message)
    {
        return true;
    }

    public function isAvailable(AdminRequest $request)
    {
        return true;
    }

    public function getDefaultConfig()
    {
        return [
            'label' => 'Delete',
            'confirmationRequired' => true,
            'buttonStyle' => 'btn-danger',
        ];
    }
}
