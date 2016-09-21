<?php

namespace Perform\MailingListBundle\Entity;

/**
 * Subscriber
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Subscriber
{
    /**
     * @var uuid
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
    protected $email;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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
     * @return Subscriber
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
     * @return Subscriber
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
     * @return Subscriber
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
     * @param bool $enabled
     *
     * @return Subscriber
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Subscriber
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Subscriber
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
