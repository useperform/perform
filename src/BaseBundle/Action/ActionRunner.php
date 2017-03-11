<?php

namespace Perform\BaseBundle\Action;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Perform\BaseBundle\Admin\AdminRegistry;
use Doctrine\ORM\EntityNotFoundException;

/**
 * ActionRunner.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunner
{
    protected $entityManager;
    protected $registry;

    public function __construct(EntityManagerInterface $entityManager, AdminRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    public function run($actionName, $entityClass, array $entityIds)
    {
        $action = $this->registry->getActionConfig($entityClass)->get($actionName);

        $entities = [];
        foreach ($entityIds as $id) {
            $entity = $this->entityManager->getRepository($entityClass)->find($id);
            if (!$entity) {
                throw new EntityNotFoundException(sprintf('Required entity "%s" for action "%s" was not found.', $id, $actionName));
            }

            if (!$action->isGranted($entity)) {
                throw new AccessDeniedException(sprintf('Action "%s" is not allowed to run on this entity.', $actionName));
            }

            $entities[] = $entity;
        }

        return $action->run($entities);
    }
}
