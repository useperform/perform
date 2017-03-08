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

    public function run($actionName, $entity, array $options)
    {
        $action = $this->registry->getAction($actionName);

        //select entity or entities
        //account for extended entities too
        $entity = $this->entityManager->getRepository($action->getTargetEntity())
                ->find($entity);

        if (!$action->isGranted($entity)) {
            throw new AccessDeniedException(sprintf('Action "%s" is not allowed to run on this entity.', $actionName));
        }

        return $action->run($entity, $options);
    }
}
