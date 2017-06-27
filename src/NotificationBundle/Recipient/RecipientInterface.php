<?php

namespace Perform\NotificationBundle\Recipient;

/**
 * RecipientInterface
 **/
interface RecipientInterface
{
    public function getId();

    public function getEmail();

    public function getForename();

    public function getSurname();

    public function getFullname();
}
