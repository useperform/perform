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
    public function findUpcoming($limit)
    {
        return $this->createQueryBuilder('e')
            ->where('e.startTime > :now')
            ->orderBy('e.startTime', 'ASC')
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
