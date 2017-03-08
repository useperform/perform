<?php

namespace Perform\BaseBundle\Action;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * ActionRunner.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunner
{
    public function __construct(EntityManagerInterface $entityManager, ActionRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    public function run($actionName, $entityId, array $options)
    {
        $entityClass = $this->registry->getTargetEntity($actionName);
        $entity = $this->entityManager->getRepository($entityClass)
                ->find($entityId);

        $action = $this->registry->getAction($actionName);

        if (!$action->isGranted($entity)) {
            throw new AccessDeniedException(sprintf('Action "%s" is not allowed to run on this entity.', $actionName));
        }

        return $action->run($entity, $options);
    }
}
