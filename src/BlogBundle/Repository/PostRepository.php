<?php

namespace Admin\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PostRepository extends EntityRepository
{
    public function findRecent($limit = 0)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.enabled = TRUE')
            ->andWhere('p.publishDate < :now')
            ->orderBy('p.publishDate', 'DESC')
            ->setParameter('now', new \DateTime());
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()
            ->getResult();
    }
}
