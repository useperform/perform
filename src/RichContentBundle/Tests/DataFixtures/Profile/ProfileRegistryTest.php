<?php

namespace Perform\RichContentBundle\Tests\DataFixtures\Profile;

use Perform\RichContentBundle\DataFixtures\Profile\ProfileInterface;
use Perform\RichContentBundle\DataFixtures\Profile\ProfileRegistry;
use Perform\RichContentBundle\DataFixtures\Profile\ProfileNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ProfileRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $registry = new ProfileRegistry([
            'simple_text' => $profile = $this->getMock(ProfileInterface::class),
        ]);
        $this->assertSame($profile, $registry->get('simple_text'));
    }

    public function testGetUnknownThrowsException()
    {
        $registry = new ProfileRegistry([
            'simple_text' => $profile = $this->getMock(ProfileInterface::class),
        ]);
        $this->setExpectedException(ProfileNotFoundException::class);
        $registry->get('long_text');
    }

    public function testGetRandom()
    {
        $registry = new ProfileRegistry($profiles = [
            'simple_text' => $this->getMock(ProfileInterface::class),
            'complex_text' => $this->getMock(ProfileInterface::class),
        ]);
        $this->assertTrue(in_array($registry->getRandom(), $profiles));
    }

    public function testGetRandomThrowsExceptionForNoProfiles()
    {
        $registry = new ProfileRegistry([]);
        $this->setExpectedException(ProfileNotFoundException::class);
        $registry->getRandom();
    }
}
