<?php

namespace Perform\BaseBundle\Settings;

use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Exception\SettingNotFoundException;

/**
 * SettingsManager.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsManager
{
    protected $entityManager;
    protected $repo;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository('PerformBaseBundle:Setting');
    }

    public function getSetting($key)
    {
        $setting = $this->repo->findOneBy(['key' => $key]);
        if (!$setting) {
            throw new SettingNotFoundException(sprintf('Setting "%s" not found.', $key));
        }

        return $setting;
    }

    public function getValue($key)
    {
        return $this->getSetting($key)->getValue();
    }

    public function setValue($key, $value)
    {
        $setting = $this->getSetting($key);
        $setting->setValue($value);
        $this->entityManager->persist($setting);
        $this->entityManager->flush();
    }
}
