<?php

namespace Perform\BaseBundle\Selector;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Admin\AdminRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Type\EntityTypeConfig;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * EntitySelector.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelector
{
    protected $entityManager;
    protected $typeConfig;
    protected $registry;

    public function __construct(EntityManagerInterface $entityManager, AdminRegistry $registry, EntityTypeConfig $typeConfig)
    {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
        $this->typeConfig = $typeConfig;
    }

    public function listContext(Request $request, $entityName)
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityName, 'e');

        $orderField = $request->query->get('sort', null);
        $direction = strtoupper($request->query->get('direction', 'asc'));
        if ($direction !== 'DESC') {
            $direction = 'ASC';
        }
        $qb = $this->maybeOrderBy($qb, $entityName, $orderField, $direction);
        if (!$qb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The sort function for %s->%s must return an instance of Doctrine\ORM\QueryBuilder.', $entityName, $orderField));
        }

        //pass the builder into the admin for potential filtering, or even
        //creating a new builder entirely

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage(10);
        $paginator->setCurrentPage($request->query->get('page', 1));

        $orderBy = [
            'field' => $orderField,
            'direction' => $direction,
        ];

        return [$paginator, $orderBy];
    }

    /**
     * @return QueryBuilder
     */
    protected function maybeOrderBy(QueryBuilder $qb, $entityName, $orderField, $direction)
    {
        if (!$orderField) {
            return $qb;
        }

        $typeConfig = $this->typeConfig->getEntityTypeConfig($entityName)->getTypes(TypeConfig::CONTEXT_LIST);
        if (!isset($typeConfig[$orderField]['options']['sort'])) {
            return $qb;
        }
        $sort = $typeConfig[$orderField]['options']['sort'];

        if ($sort === true) {
            return $qb->orderBy('e.'.$orderField, $direction);
        }
        if (is_callable($sort)) {
            return $sort($qb, $direction);
        }

        return $qb;
    }
}
