<?php

namespace Perform\BaseBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Grant permission to all crud contexts by default.
 *
 * It allows access for both the crud name
 * is_granted('VIEW', 'some_crud')
 * as well as a specific entity object
 * is_granted('VIEW', $entity)
 *
 * This voter is designed to be used with the 'unanimous' security strategy.
 * Add other voters to restrict access to certain contexts and entities.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudVoter extends Voter
{
    protected $crudRegistry;
    protected $urlGenerator;
    protected $em;
    // results of doctrine metadata and crud checks cached for speed
    protected $classSupportsCache = [];

    public function __construct(CrudRegistry $crudRegistry, CrudUrlGeneratorInterface $urlGenerator, EntityManagerInterface $em)
    {
        $this->crudRegistry = $crudRegistry;
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
    }

    public function supports($attribute, $subject)
    {
        if (is_string($subject) && $this->crudRegistry->has($subject)) {
            return true;
        }
        if (!is_object($subject)) {
            return false;
        }

        $class = get_class($subject);
        if (!isset($this->classSupportsCache[$class])) {
            $this->classSupportsCache[$class] =
                                              // is a doctrine entity
                                              !$this->em->getMetadataFactory()->isTransient($class)
                                              // has a crud
                                              && $this->crudRegistry->hasForEntity($class);
        }

        return $this->classSupportsCache[$class];
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return is_string($subject)
            ? $this->voteOnCrudName($attribute, $subject)
            : in_array($attribute, ['VIEW', 'EDIT', 'DELETE']);
    }

    private function voteOnCrudName($attribute, $crudName)
    {
        if ($attribute === 'DELETE') {
            return true;
        }

        return in_array($attribute, ['VIEW', 'EDIT']) &&
            $this->urlGenerator->routeExists($crudName, strtolower($attribute));
    }
}
