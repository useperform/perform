<?php

namespace Perform\BaseBundle\Action;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityNotFoundException;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * ActionRunner.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunner
{
    protected $entityManager;
    protected $store;

    public function __construct(EntityManagerInterface $entityManager, ConfigStoreInterface $store)
    {
        $this->entityManager = $entityManager;
        $this->store = $store;
    }

    public function run($actionName, $entityClass, array $entityIds, array $options = [])
    {
        $action = $this->store->getActionConfig($entityClass)->get($actionName);

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

        return $action->run($entities, $options);
    }
}
