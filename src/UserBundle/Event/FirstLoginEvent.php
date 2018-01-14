<?php

namespace Perform\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Perform\UserBundle\Entity\User;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FirstLoginEvent extends Event
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
