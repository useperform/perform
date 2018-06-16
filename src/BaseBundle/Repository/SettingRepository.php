<?php

namespace Perform\BaseBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Perform\BaseBundle\Exception\SettingNotFoundException;
use Perform\BaseBundle\Entity\Setting;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingRepository extends EntityRepository
{
    public function getRequiredValue($key)
    {
        $setting = $this->findOneBy(['key' => $key, 'global' => true]);
        if (!$setting) {
            throw new SettingNotFoundException(sprintf('Setting "%s" was not found in the database.', $key));
        }

        return $setting->getValue();
    }

    public function setValue($key, $value)
    {
        $setting = $this->findOneBy(['key' => $key, 'global' => true]);
        if (!$setting) {
            $setting = new Setting($key);
            $setting->setGlobal(true);
        }
        $setting->setValue($value);

        $this->_em->persist($setting);
        $this->_em->flush();
    }
}
