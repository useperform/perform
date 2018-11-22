<?php

namespace Perform\BaseBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingRepository extends EntityRepository
{
    public function findSetting($key)
    {
        return $this->findOneBy(['key' => $key, 'user' => null]);
    }

    public function findUserSetting(UserInterface $user, $key)
    {
        return $this->findOneBy(['key' => $key, 'user' => $user]);
    }
}
