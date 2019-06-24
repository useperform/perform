<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;
use Perform\BaseBundle\Repository\SettingRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Entity\Setting;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DoctrineManager implements SettingsManagerInterface, WriteableSettingsManagerInterface
{
    protected $repo;
    protected $em;

    public function __construct(SettingRepository $repo, EntityManagerInterface $em)
    {
        $this->repo = $repo;
        $this->em = $em;
    }

    public function getValue($key, $default = null)
    {
        try {
            return $this->getRequiredValue($key);
        } catch (SettingNotFoundException $e) {
            return $default;
        }
    }

    public function getRequiredValue($key)
    {
        $setting = $this->repo->findSetting($key);
        if (!$setting) {
            throw new SettingNotFoundException(sprintf('Setting "%s" was not found in the database.', $key));
        }

        return $setting->getValue();
    }

    public function setValue($key, $value)
    {
        $setting = $this->repo->findSetting($key);
        if (!$setting) {
            $setting = new Setting($key);
        }
        $setting->setValue($value);

        $this->em->persist($setting);
        $this->em->flush();
    }

    public function getUserValue(UserInterface $user, $key, $default = null)
    {
        try {
            return $this->getRequiredUserValue($user, $key);
        } catch (SettingNotFoundException $e) {
            return $default;
        }
    }

    public function getRequiredUserValue(UserInterface $user, $key)
    {
        $setting = $this->repo->findOneBy(['key' => $key, 'user' => $user]);
        if (!$setting) {
            throw new SettingNotFoundException(sprintf('User setting "%s" was not found in the database.', $key));
        }

        return $setting->getValue();
    }

    public function setUserValue(UserInterface $user, $key, $value)
    {
        $setting = $this->repo->findUserSetting($user, $key);
        if (!$setting) {
            $setting = new Setting($key);
            $setting->setUser($user);
        }
        $setting->setValue($value);

        $this->em->persist($setting);
        $this->em->flush();
    }
}
