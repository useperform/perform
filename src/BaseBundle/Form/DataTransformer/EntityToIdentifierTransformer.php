<?php

namespace Perform\BaseBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transform doctrine entities to identifiers and back again.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityToIdentifierTransformer implements DataTransformerInterface
{
    protected $em;
    protected $entityClass;

    public function __construct(EntityManagerInterface $em, $entityClass)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
    }

    /**
     * Transform a doctrine entity into an identifier.
     */
    public function transform($entity)
    {
        if ($entity === null) {
            return;
        }
        if (!is_object($entity)) {
            throw new UnexpectedTypeException($entity, 'object');
        }

        return $this->em->getUnitOfWork()->getSingleIdentifierValue($entity);
    }

    /**
     * Transform an identifier into a doctrine entity.
     */
    public function reverseTransform($id)
    {
        return $id === null ? null : $this->em->getRepository($this->entityClass)->find($id);
    }
}
