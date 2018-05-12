<?php

namespace Perform\UserBundle\Security;

use Perform\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserManager
{
    protected $em;
    protected $passwordExpiryLength;

    public function __construct(EntityManagerInterface $em, $passwordExpiryLength)
    {
        $this->em = $em;
        $this->passwordExpiryLength = $passwordExpiryLength;
    }

    /**
     * @param User   $user
     * @param string $newPassword The new password as plain text
     */
    public function updatePassword(User $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $expiresAt = new \DateTime();
        $expiresAt->add(\DateInterval::createFromDateString(sprintf('%s seconds', $this->passwordExpiryLength)));
        $user->setPasswordExpiresAt($expiresAt);
        $this->em->persist($user);
        $this->em->flush();
    }
}
