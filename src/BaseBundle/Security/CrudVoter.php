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
 * If a context exists, it will grant access.
 * Otherwise, it will abstain from voting.
 * It will never deny access.
 *
 * It allows access for both the crud name
 * is_granted('VIEW', 'some_crud')
 * as well as a specific entity object
 * is_granted('VIEW', $entity)
 *
 * This voter is designed to be used with the 'unanimous' security strategy.
 * Any other strategy could result in security issues, as it may grant
 * access even after another voter has denied access.
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
        // crud name
        // can vote if the crud context route exists (delete is an action, assumed to exist)
        if (is_string($subject) && $this->crudRegistry->has($subject)) {
            if ($attribute === 'DELETE') {
                return true;
            }

            return in_array($attribute, ['CREATE', 'VIEW', 'EDIT']) &&
                $this->urlGenerator->routeExists($subject, strtolower($attribute));
        }

        if (!is_object($subject)) {
            return false;
        }

        // entity class
        // can vote if it has a crud and the attribute is one of the
        // crud contexts that is checked with an entity object (not CREATE, LIST, or EXPORT)
        $class = get_class($subject);
        if (!isset($this->classSupportsCache[$class])) {
            $this->classSupportsCache[$class] =
                                              // is a doctrine entity
                                              !$this->em->getMetadataFactory()->isTransient($class)
                                              // has a crud
                                              && $this->crudRegistry->hasForEntity($class);
        }

        return $this->classSupportsCache[$class] && in_array($attribute, ['VIEW', 'EDIT', 'DELETE']);
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return true;
    }
}
