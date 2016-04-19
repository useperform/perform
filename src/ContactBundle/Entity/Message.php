<?php

namespace Admin\ContactBundle\Entity;

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
    protected $timeSent;

    /**
     * @var int
     */
    protected $status;

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
     * @param \DateTime $timeSent
     *
     * @return Message
     */
    public function setTimeSent(\DateTime $timeSent)
    {
        $this->timeSent = $timeSent;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimeSent()
    {
        return $this->timeSent;
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
}
