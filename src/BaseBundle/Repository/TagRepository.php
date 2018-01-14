<?php

namespace Perform\BaseBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TagRepository extends EntityRepository
{

    public function queryByDiscriminator($discriminator)
    {
        return $this->createQueryBuilder('t')
            ->where('t.discriminator = :discriminator')
            ->setParameter('discriminator', $discriminator);
    }
}
