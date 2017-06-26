<?php

namespace Perform\NotificationBundle\Recipient;

/**
 * SimpleRecipient.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleRecipient implements RecipientInterface
{
    public function __construct($id, $email, $firstName = null, $lastName = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getForename()
    {
        return $this->firstName;
    }

    public function getSurname()
    {
        return $this->lastName;
    }

    public function getFullname()
    {
        return $this->firstName.' '.$this->lastName;
    }
}
