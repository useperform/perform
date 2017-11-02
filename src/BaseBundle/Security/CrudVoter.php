<?php

namespace Perform\BaseBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;

/**
 * Grant permission to execute actions to entities by default.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudVoter extends Voter
{
    protected $adminRegistry;
    protected $urlGenerator;

    public function __construct(AdminRegistry $adminRegistry, CrudUrlGeneratorInterface $urlGenerator)
    {
        $this->adminRegistry = $adminRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports($attribute, $subject)
    {
        return $this->adminRegistry->hasAdmin($subject);
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
