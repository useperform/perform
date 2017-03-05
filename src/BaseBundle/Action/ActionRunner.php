<?php

namespace Perform\BaseBundle\Action;

use Doctrine\ORM\EntityManagerInterface;

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

    public function run($action, $entity, array $options)
    {
        $action = $this->registry->getAction($action);

        //select entity or entities
        //account for extended entities too
        $entity = $this->entityManager->getRepository($action->getTargetEntity())
                ->find($entity);

        return $action->run($entity, $options);
    }
}
