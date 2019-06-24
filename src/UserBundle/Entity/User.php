<?php

namespace Perform\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class User implements UserInterface, RecipientInterface
{
    /**
     * @var guid
     */
    protected $id;

    /**
     * @var string
     */
    protected $forename;

    /**
     * @var string
     */
    protected $surname;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     */
    public $passwordExpiresAt;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var array
     */
    protected $roles = ['ROLE_USER'];

    /**
     * @var \DateTime|null
     */
    protected $lastLogin;

    /**
     * @var Collection
     */
    protected $resetTokens;

    public function __construct()
    {
        $this->resetTokens = new ArrayCollection();
    }

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $forename
     *
     * @return User
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * @return string
     */
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->forename . ' ' . $this->surname;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = mb_convert_case($email, MB_CASE_LOWER, mb_detect_encoding($email));

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @param string $password the encoded password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string The encoded password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        //reset the stored password hash so an update will occur
        //(plain password is not mapped by doctrine)
        $this->password = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param \DateTime $passwordExpiresAt
     *
     * @return User
     */
    public function setPasswordExpiresAt(\DateTime $passwordExpiresAt)
    {
        $this->passwordExpiresAt = $passwordExpiresAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordExpiresAt()
    {
        return $this->passwordExpiresAt;
    }

    /**
     * @return bool
     */
    public function isPasswordExpired()
    {
        if (!$this->passwordExpiresAt) {
            return true;
        }

        return $this->passwordExpiresAt < new \DateTime();
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return null (use bcrypt)
     */
    public function getSalt()
    {
        return;
    }

    /**
     * @param array
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     */
    public function removeRole($role)
    {
        if (!in_array($role, $this->roles, true)) {
            return;
        }

        unset($this->roles[array_search($role, $this->roles)]);
        $this->roles = array_values($this->roles);

        return $this;
    }

    /**
     * Return true if the user has the given role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles(), true);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param \DateTime|null $lastLogin
     *
     * @return User
     */
    public function setLastLogin(\DateTime $lastLogin = null)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param ResetToken $token
     *
     * @return User
     */
    public function addResetToken(ResetToken $token)
    {
        $this->resetTokens[] = $resetToken;
        $resetToken->setUser($this);

        return $this;
    }

    /**
     * @param ResetToken $token
     */
    public function removeResetToken(ResetToken $token)
    {
        $this->items->removeElement($token);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getResetTokens()
    {
        return $this->resetTokens;
    }
}
