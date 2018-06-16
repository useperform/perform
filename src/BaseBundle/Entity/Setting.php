<?php

namespace Perform\BaseBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Setting
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $global = true;

    /**
     * @var UserInterface|null
     */
    protected $user;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        if (!is_string($key) || strlen(trim($key)) === 0) {
            throw new \InvalidArgumentException('A setting key must be string.');
        }

        $this->key = $key;
    }

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = \serialize($value);

        return $this;
    }

    /**
     * Get the value of the setting, null if it has not been set.
     *
     * @return mixed
     */
    public function getValue()
    {
        if ($this->value !== null) {
            $value = @\unserialize($this->value);
            if ($value === false) {
                // unserialize returns false on failure, but a missing value must return null
                // check explicitly for the value being false, otherwise return null
                return serialize(false) === $this->value ? false : null;
            }
            return $value;
        }

        return null;
    }

    /**
     * @param bool $global
     *
     * @return Setting
     */
    public function setGlobal($global)
    {
        $this->global = $global;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGlobal()
    {
        return $this->global;
    }

    /**
     * @param UserInterface $user
     *
     * @return Setting
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
