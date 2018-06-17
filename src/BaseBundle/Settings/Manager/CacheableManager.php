<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CacheableManager implements SettingsManagerInterface
{
    protected $manager;
    protected $cache;
    protected $cacheExpiry;

    public function __construct(SettingsManagerInterface $manager, CacheItemPoolInterface $cache, $cacheExpiry = 0)
    {
        $this->manager = $manager;
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
        $cachedValue = $this->cache->getItem($key);
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
        $this->cache->deleteItem($key);

        return $this->manager->setValue($key, $value);
    }
}
