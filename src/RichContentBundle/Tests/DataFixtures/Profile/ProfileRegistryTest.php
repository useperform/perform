<?php

namespace Perform\RichContentBundle\Tests\DataFixtures\Profile;

use PHPUnit\Framework\TestCase;
use Perform\RichContentBundle\DataFixtures\Profile\ProfileInterface;
use Perform\RichContentBundle\DataFixtures\Profile\ProfileRegistry;
use Perform\RichContentBundle\DataFixtures\Profile\ProfileNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ProfileRegistryTest extends TestCase
{
    public function testGet()
    {
        $registry = new ProfileRegistry([
            'simple_text' => $profile = $this->createMock(ProfileInterface::class),
        ]);
        $this->assertSame($profile, $registry->get('simple_text'));
    }

    public function testGetUnknownThrowsException()
    {
        $registry = new ProfileRegistry([
            'simple_text' => $profile = $this->createMock(ProfileInterface::class),
        ]);
        $this->expectException(ProfileNotFoundException::class);
        $registry->get('long_text');
    }

    public function testGetRandom()
    {
        $registry = new ProfileRegistry($profiles = [
            'simple_text' => $this->createMock(ProfileInterface::class),
            'complex_text' => $this->createMock(ProfileInterface::class),
        ]);
        $this->assertTrue(in_array($registry->getRandom(), $profiles));
    }

    public function testGetRandomThrowsExceptionForNoProfiles()
    {
        $registry = new ProfileRegistry([]);
        $this->expectException(ProfileNotFoundException::class);
        $registry->getRandom();
    }
}
