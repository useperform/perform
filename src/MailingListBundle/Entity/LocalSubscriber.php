<?php

namespace Perform\MailingListBundle\Entity;

/**
 * LocalSubscriber
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalSubscriber
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var LocalList
     */
    protected $list;

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $firstName|null
     *
     * @return Subscriber
     */
    public function setFirstName($firstName = null)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $email
     *
     * @return Subscriber
     */
    public function setEmail($email)
    {
        $this->email = $email;

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

    /**
     * @param LocalList $list
     *
     * @return LocalSubscriber
     */
    public function setList(LocalList $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return LocalList
     */
    public function getList()
    {
        return $this->list;
    }
}
