<?php

namespace Perform\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TagRepository extends EntityRepository
{
    public function findPublished()
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.posts', 'p')
            ->where('p.enabled = TRUE')
            ->andWhere('p.publishDate < :now')
            ->orderBy('t.name', 'ASC')
            ->setParameter('now', new \DateTime());

        return $qb->getQuery()
            ->getResult();
    }
}
