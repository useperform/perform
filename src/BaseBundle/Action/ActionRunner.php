<?php

namespace Perform\BaseBundle\Action;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityNotFoundException;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * ActionRunner.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunner
{
    protected $entityManager;
    protected $store;
    protected $authChecker;

    public function __construct(EntityManagerInterface $entityManager, ConfigStoreInterface $store, AuthorizationCheckerInterface $authChecker)
    {
        $this->entityManager = $entityManager;
        $this->store = $store;
        $this->authChecker = $authChecker;
    }

    public function run($crudName, $actionName, array $entityIds, array $options = [])
    {
        $action = $this->store->getActionConfig($crudName)->get($actionName);
        $entityClass = $this->store->getEntityClass($crudName);
        $crudRequest = new CrudRequest($crudName, CrudRequest::CONTEXT_ACTION);

        $entities = [];
        foreach ($entityIds as $id) {
            $entity = $this->entityManager->getRepository($entityClass)->find($id);
            if (!$entity) {
                throw new EntityNotFoundException(sprintf('Required entity "%s" for action "%s" was not found.', $id, $actionName));
            }

            if (!$action->isGranted($entity, $this->authChecker)) {
                throw new AccessDeniedException(sprintf('Action "%s" is not allowed to run on this entity.', $actionName));
            }

            $entities[] = $entity;
        }

        return $action->run($crudRequest, $entities, $options);
    }
}
