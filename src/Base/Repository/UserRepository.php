<?php

namespace Admin\Base\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserRepository extends EntityRepository
{
    public function findByEmails(array $emails)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email IN (:emails)')
            ->setParameter('emails', $emails)
            ->getQuery()
            ->getResult();
    }
}
