<?php

namespace Perform\UserBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManagerInterface;
use Perform\UserBundle\Entity\User;

/**
 * Set lastLogin when logging in.
 *
 * If this is the user's first login, add a flag to the session.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoginListener
{
    protected $em;

    const FIRST_LOGIN = 'perform_user.first_login';

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getLastLogin() instanceof \DateTime) {
            $session = $event->getRequest()->getSession();
            if ($session) {
                $session->set(static::FIRST_LOGIN, true);
            }
        }

        $user->setLastLogin(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();
    }
}
