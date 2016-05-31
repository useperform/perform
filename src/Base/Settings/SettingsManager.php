<?php

namespace Admin\Base\Settings;

use Doctrine\ORM\EntityManagerInterface;

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
        $this->repo = $entityManager->getRepository('AdminBaseBundle:Setting');
    }

    public function getValue($key)
    {
        return $this->repo->findOneBy(['key' => $key])->getValue();
    }

    public function setValue($key, $value)
    {
        $setting = $this->repo->findOneBy(['key' => $key]);
        $setting->setValue($value);
        $this->entityManager->persist($setting);
        $this->entityManager->flush();
    }
}
