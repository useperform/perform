<?php

namespace Perform\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
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
