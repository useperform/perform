<?php

namespace Perform\BaseBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;

/**
 * Grant permission to execute actions to entities by default.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudVoter extends Voter
{
    protected $crudRegistry;
    protected $urlGenerator;

    public function __construct(CrudRegistry $crudRegistry, CrudUrlGeneratorInterface $urlGenerator)
    {
        $this->crudRegistry = $crudRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports($attribute, $subject)
    {
        return $this->crudRegistry->hasCrud($subject);
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
        case 'VIEW':
            return $this->urlGenerator->routeExists($subject, 'view');
        case 'EDIT':
            return $this->urlGenerator->routeExists($subject, 'edit');
        case 'DELETE':
            return true;
        default:
            return false;
        }
    }
}
