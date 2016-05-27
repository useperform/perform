<?php

namespace Admin\Base\Settings;

use Admin\Base\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;

/**
 * SettingsImporter adds and updates settings in the database according to a
 * their defined specification.
 *
 * If a setting definition is different to the definition in the database
 * (i.e. everything except the value), it will be updated.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsImporter
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository('AdminBaseBundle:Setting');
    }

    public function import(Setting $setting)
    {
        $existing = $this->find($setting->getKey());
        if (!$existing) {
            $this->entityManager->persist($setting);
            $this->entityManager->flush();

            return;
        }

        if (!$existing->requiresUpdate($setting)) {
            return;
        }

        $existing->update($setting);
        $this->entityManager->persist($existing);
        $this->entityManager->flush();
    }

    protected function find($key)
    {
        if (!is_string($key)) {
        }

        return $this->repo->findOneBy(['key' => $key]);
    }
}
