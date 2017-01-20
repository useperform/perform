<?php

namespace Perform\ContactBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Message
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Message
{
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_SPAM = 3;

    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \Datetime
     */
    protected $createdAt;

    /**
     * @var \Datetime
     */
    protected $updatedAt;

    /**
     * @var int
     */
    protected $status;

    protected $spamReports;

    public function __construct()
    {
        $this->spamReports = new ArrayCollection();
    }

    /**
     * @var uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Message
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $email
     *
     * @return Message
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
     * @param string $message
     *
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Message
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
     * @return Message
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
     * @param int $status
     *
     * @return Message
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isSpam()
    {
        return $this->status === static::STATUS_SPAM;
    }

    /**
     * @return Collection
     */
    public function getSpamReports()
    {
        return $this->spamReports;
    }

    /**
     * @param SpamReport $spamReport
     *
     * @return Message
     */
    public function addSpamReport(SpamReport $spamReport)
    {
        $this->spamReports[] = $spamReport;

        return $this;
    }
}
