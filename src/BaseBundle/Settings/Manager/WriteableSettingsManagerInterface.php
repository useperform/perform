<?php

namespace Perform\BaseBundle\Settings\Manager;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface WriteableSettingsManagerInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setValue($key, $value);

    /**
     * @param UserInterface $user
     * @param string        $key
     * @param mixed         $value
     */
    public function setUserValue(UserInterface $user, $key, $value);
}
