<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;
use Perform\BaseBundle\Exception\ReadOnlySettingsException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CacheableManager implements SettingsManagerInterface, WriteableSettingsManagerInterface
{
    protected $manager;
    protected $writeable;
    protected $cache;
    protected $cacheExpiry;

    public function __construct(SettingsManagerInterface $manager, CacheItemPoolInterface $cache, $cacheExpiry = 0)
    {
        $this->manager = $manager;
        $this->writeable = $manager instanceof WriteableSettingsManagerInterface;
        $this->cache = $cache;
        $this->cacheExpiry = (int) $cacheExpiry;
    }

    public function getInnerManager()
    {
        return $this->manager;
    }

    public function getValue($key, $default = null)
    {
        try {
            return $this->getRequiredValue($key);
        } catch (SettingNotFoundException $e) {
            // do not cache the default value
            return $default;
        }
    }

    public function getRequiredValue($key)
    {
        $cachedValue = $this->cache->getItem($this->cacheKey($key));
        if ($cachedValue->isHit()) {
            return $cachedValue->get();
        }

        $value = $this->manager->getRequiredValue($key);
        $cachedValue->set($value);
        if ($this->cacheExpiry > 0) {
            $cachedValue->expiresAfter($this->cacheExpiry);
        }
        $this->cache->save($cachedValue);

        return $value;
    }

    public function setValue($key, $value)
    {
        $this->assertWriteable();
        $this->cache->deleteItem($this->cacheKey($key));

        return $this->manager->setValue($key, $value);
    }

    private function assertWriteable()
    {
        if (!$this->writeable) {
            throw new ReadOnlySettingsException(sprintf('%s is read-only. You should create a manager that implements %s to write settings.', get_class($this->manager), WriteableSettingsManagerInterface::class));
        }
    }

    protected function cacheKey($key)
    {
        return urlencode($key);
    }

    protected function userCacheKey(UserInterface $user, $key)
    {
        return urlencode($key).'_'.urlencode($user->getUsername());
    }

    public function getUserValue(UserInterface $user, $key, $default = null)
    {
        try {
            return $this->getRequiredUserValue($user, $key);
        } catch (SettingNotFoundException $e) {
            // do not cache the default value
            return $default;
        }
    }

    public function getRequiredUserValue(UserInterface $user, $key)
    {
        $cachedValue = $this->cache->getItem($this->userCacheKey($user, $key));
        if ($cachedValue->isHit()) {
            return $cachedValue->get();
        }

        $value = $this->manager->getRequiredUserValue($user, $key);
        $cachedValue->set($value);
        if ($this->cacheExpiry > 0) {
            $cachedValue->expiresAfter($this->cacheExpiry);
        }
        $this->cache->save($cachedValue);

        return $value;
    }

    public function setUserValue(UserInterface $user, $key, $value)
    {
        $this->assertWriteable();
        $this->cache->deleteItem($this->userCacheKey($user, $key));

        return $this->manager->setUserValue($user, $key, $value);
    }
}
