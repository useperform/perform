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
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $requiredRole;

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
        if (!is_string($key)) {
            throw new \InvalidArgumentException('A setting key must be string.');
        }

        if (!strlen($key) === 0 || !preg_match('/^[_a-z]+$/', $key)) {
            throw new \InvalidArgumentException(sprintf('The key for a setting must be string containing lower case characters and underscores, "%s" given.', $key));
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
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return Setting
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param string $type
     *
     * @return Setting
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
     * @param string $requiredRole
     *
     * @return Setting
     */
    public function setRequiredRole($requiredRole)
    {
        $this->requiredRole = $requiredRole;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequiredRole()
    {
        return $this->requiredRole;
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
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Check if this setting requires to be updated, according to a new setting
     * definition.
     *
     * @param Setting $new The new setting definition
     */
    public function requiresUpdate(Setting $new)
    {
        if ($new->getKey() !== $this->key) {
            return false;
        }

        if ($new->isGlobal() !== $this->global
            || $new->getRequiredRole() !== $this->requiredRole
            || $new->getType() !== $this->type
            || $new->getDefaultValue() !== $this->defaultValue
        ) {
            return true;
        }

        return false;
    }

    /**
     * Update this setting according to a new setting definition.
     *
     * @param Setting $new The new setting definition
     */
    public function update(Setting $new)
    {
        if ($new->getKey() !== $this->key) {
            throw new \InvalidArgumentException(sprintf('Unable to update setting "%s" with setting definition "%s", keys must match.', $this->key, $new->getKey()));
        }

        $this->setGlobal($new->isGlobal());
        $this->setRequiredRole($new->getRequiredRole());
        $this->setType($new->getType());
        $this->setDefaultValue($new->getDefaultValue());
    }
}
