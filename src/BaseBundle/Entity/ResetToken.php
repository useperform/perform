<?php

namespace Perform\BaseBundle\Entity;

/**
 * ResetToken.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ResetToken
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $secret
     *
     * @return ResetToken
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param User $user
     *
     * @return ResetToken
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \DateTime $expiresAt
     *
     * @return ResetToken
     */
    public function setExpiresAt(\DateTime $expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
