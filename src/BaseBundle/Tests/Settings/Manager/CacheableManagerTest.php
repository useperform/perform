<?php

namespace Perform\BaseBundle\Tests\Settings;

use Perform\BaseBundle\Settings\Manager\CacheableManager;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CacheableManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $cache;
    protected $innerManager;
    protected $manager;

    public function setUp()
    {
        $this->cache = $this->getMock(CacheItemPoolInterface::class);
        $this->innerManager = $this->getMock(SettingsManagerInterface::class);
        $this->manager = new CacheableManager($this->innerManager, $this->cache);
    }

    public function testImplementsInterfaces()
    {
        $this->assertInstanceOf(SettingsManagerInterface::class, $this->manager);
    }

    public function testGetInnerManager()
    {
        $this->assertSame($this->innerManager, $this->manager->getInnerManager());
    }

    private function expectItem($key, $isHit, $value = null)
    {
        $item = $this->getMock(CacheItemInterface::class);
        $item->expects($this->any())
            ->method('isHit')
            ->will($this->returnValue($isHit));
        $item->expects($this->any())
            ->method('get')
            ->will($this->returnValue($value));

        $this->cache->expects($this->any())
            ->method('getItem')
            ->with($key)
            ->will($this->returnValue($item));

        return $item;
    }

    public function testGetValueCacheMiss()
    {
        $item = $this->expectItem('some_setting', false);
        $this->innerManager->expects($this->once())
            ->method('getRequiredValue')
            ->with('some_setting')
            ->will($this->returnValue('some_value'));
        $item->expects($this->once())
            ->method('set')
            ->with('some_value');
        $this->cache->expects($this->once())
            ->method('save')
            ->with($item);

        $this->assertSame('some_value', $this->manager->getValue('some_setting'));
    }

    public function testGetValueCacheMissWithExpiryTime()
    {
        $this->manager = new CacheableManager($this->innerManager, $this->cache, 30);

        $item = $this->expectItem('some_setting', false);
        $this->innerManager->expects($this->once())
            ->method('getRequiredValue')
            ->with('some_setting')
            ->will($this->returnValue('some_value'));
        $item->expects($this->once())
            ->method('set')
            ->with('some_value');
        $item->expects($this->once())
            ->method('expiresAfter')
            ->with(30);
        $this->cache->expects($this->once())
            ->method('save')
            ->with($item);

        $this->assertSame('some_value', $this->manager->getValue('some_setting'));
    }

    public function testGetValueCacheHit()
    {
        $this->expectItem('some_setting', true, 'cached_value');
        $this->innerManager->expects($this->never())
            ->method('getRequiredValue');

        $this->assertSame('cached_value', $this->manager->getValue('some_setting', 'some_default'));
    }

    public function testSetValue()
    {
        $this->innerManager->expects($this->once())
            ->method('setValue')
            ->with('some_setting', 'new_value');
        $this->cache->expects($this->once())
            ->method('deleteItem')
            ->with('some_setting');

        $this->manager->setValue('some_setting', 'new_value');
    }
}
