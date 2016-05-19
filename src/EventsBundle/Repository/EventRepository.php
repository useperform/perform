<?php

namespace Admin\EventsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EventRepository extends EntityRepository
{
    public function findUpcoming($limit = 0)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.startTime > :now')
            ->orderBy('e.startTime', 'ASC')
            ->setParameter('now', new \DateTime());
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findPast($limit = 0)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.startTime < :now')
            ->orderBy('e.startTime', 'DESC')
            ->setParameter('now', new \DateTime());
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()
            ->getResult();
    }
}
