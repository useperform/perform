<?php

namespace Perform\BaseBundle\Selector;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Admin\AdminRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * EntitySelector
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelector
{
    protected $entityManager;
    protected $registry;

    public function __construct(EntityManagerInterface $entityManager, AdminRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    public function listContext(Request $request, $entityName)
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityName, 'e');
        $direction = strtoupper($request->query->get('direction', 'asc'));
        if ($direction !== 'DESC') {
            $direction = 'ASC';
        }
        if ($orderField = $request->query->get('sort', null)) {
            $qb->orderBy('e.'.$orderField, $direction);
        }
        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage(10);
        $paginator->setCurrentPage($request->query->get('page', 1));

        $orderBy = [
            'field' => $orderField,
            'direction' => $direction,
        ];

        return [$paginator, $orderBy];
    }
}
