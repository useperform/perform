<?php

namespace Admin\Base\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Admin\NotificationBundle\RecipientInterface;

/**
 * User
 *
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
     * @var string
     */
    protected $email;

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
        $this->email = strtolower($email);

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
     * @return array
     */
    public function getRoles()
    {
        return [
            'ROLE_USER',
        ];
    }
}
