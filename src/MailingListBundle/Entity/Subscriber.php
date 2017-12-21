<?php

namespace Perform\MailingListBundle\Entity;

use Perform\MailingListBundle\Exception\MissingAttributeException;

/**
 * Represents a subscriber due to be added to a list.
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
    protected $email;

    /**
     * @var string
     */
    protected $connectorName;

    /**
     * @var string
     */
    protected $list;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = mb_convert_case($email, MB_CASE_LOWER, mb_detect_encoding($email));
    }

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $connectorName
     *
     * @return Subscriber
     */
    public function setConnectorName($connectorName)
    {
        $this->connectorName = $connectorName;

        return $this;
    }

    /**
     * @return string
     */
    public function getConnectorName()
    {
        return $this->connectorName;
    }

    /**
     * @param string $list
     *
     * @return Subscriber
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $attributes
     *
     * @return Subscriber
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            throw new MissingAttributeException(sprintf('Missing required subscriber attribute "%s"', $name));
        }

        return $this->attributes[$name];
    }

    /**
     * @return mixed|null
     */
    public function getOptionalAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
}
