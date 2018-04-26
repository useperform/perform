<?php

namespace Perform\NotificationBundle\Entity;

use Perform\NotificationBundle\Notification;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A notification for a user.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class NotificationLog
{
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;

    /**
     * @var guid
     */
    private $id;

    /**
     * @var UserInterface
     */
    private $recipient;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var int
     */
    private $status = self::STATUS_UNREAD;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $content;

    /**
     * @return guid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param UserInterface $recipient
     *
     * @return NotificationLog
     */
    public function setRecipient(UserInterface $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return NotificationLog
     */
    public function setCreatedAt($createdAt)
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
     * @return NotificationLog
     */
    public function setUpdatedAt($updatedAt)
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
     * @return NotificationLog
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
     * @param string $type
     *
     * @return NotificationLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $content
     *
     * @return NotificationLog
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
