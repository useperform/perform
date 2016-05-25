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
            ->orderBy('p.publishDate', 'DESC');
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()
            ->getResult();
    }
}
