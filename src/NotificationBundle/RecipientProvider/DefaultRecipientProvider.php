<?php

namespace Admin\NotificationBundle\RecipientProvider;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * DefaultRecipientProvider assumes your default user entity
 * implements RecipientInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DefaultRecipientProvider implements ActiveRecipientProviderInterface
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getActiveRecipient()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        return $token->getUser();
    }
}
