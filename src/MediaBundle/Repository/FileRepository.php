<?php

namespace Perform\MediaBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileRepository extends EntityRepository
{
    public function findPage($page)
    {
        $page = (int) $page;
        $perPage = 10;
        $query = $this->createQueryBuilder('f')
               ->orderBy('f.updatedAt', 'DESC')
               ->getQuery();

        $query->setMaxResults($perPage);
        $query->setFirstResult(($page * $perPage) - $perPage);

        return $query->getResult();
    }
}
