<?php

namespace Perform\BaseBundle\Settings\Manager;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface SettingsManagerInterface
{
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValue($key, $default = null);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getRequiredValue($key);

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setValue($key, $value);

    /**
     * @param UserInterface $user
     * @param string        $key
     * @param mixed         $default
     *
     * @return mixed
     */
    public function getUserValue(UserInterface $user, $key, $default = null);

    /**
     * @param UserInterface $user
     * @param string        $key
     *
     * @return mixed
     */
    public function getRequiredUserValue(UserInterface $user, $key);

    /**
     * @param UserInterface $user
     * @param string        $key
     * @param mixed         $value
     */
    public function setUserValue(UserInterface $user, $key, $value);
}
