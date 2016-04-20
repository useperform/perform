<?php

namespace Admin\NotificationBundle\RecipientProvider;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * CurrentUserProvider assumes your default user entity
 * implements RecipientInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CurrentUserProvider implements RecipientProviderInterface
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getRecipients(array $criteria = [])
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }
        $user = $token->getUser();

        return $user ? [$user] : [];
    }
}
