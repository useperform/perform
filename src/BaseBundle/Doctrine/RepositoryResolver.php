<?php

namespace Perform\BaseBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Get the correct doctrine repository for an entity, even if the entity has been extended.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RepositoryResolver
{
    protected $resolver;

    public function __construct(EntityManagerInterface $em, EntityResolver $resolver)
    {
        $this->em = $em;
        $this->resolver = $resolver;
    }

    /**
     * Get the correct repository for a possibly extended doctrine entity.
     *
     * @param mixed $entity
     */
    public function getRepository($entity)
    {
        return $this->em->getRepository($this->resolver->resolve($entity));
    }
}
