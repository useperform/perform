<?php

namespace Perform\NotificationBundle\Tests\RecipientProvider;

use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Settings\SettingsManager;
use Perform\NotificationBundle\RecipientProvider\SettingsProvider;
use Perform\NotificationBundle\Recipient\RecipientInterface;
use Perform\BaseBundle\Repository\UserRepository;
use Perform\NotificationBundle\Recipient\SimpleRecipient;

/**
 * SettingsProviderTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $repo;
    protected $settings;
    protected $provider;

    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->repo = $this->getMockBuilder(UserRepository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repo));

        $this->settings = $this->getMockBuilder(SettingsManager::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->provider = new SettingsProvider($this->em, $this->settings);
    }

    protected function mockRecipient($email)
    {
        $user = $this->getMock(RecipientInterface::class);
        $user->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($email));

        return $user;
    }

    protected function expectSetting($key, $value)
    {
        $this->settings->expects($this->any())
            ->method('getValue')
            ->with($key)
            ->will($this->returnValue($value));
    }

    public function testSingleUserRecipient()
    {
        $user = $this->mockRecipient('me@example.com');
        $this->repo->expects($this->once())
            ->method('findByEmails')
            ->with(['me@example.com'])
            ->will($this->returnValue([$user]));
        $key = 'some_bundle.send_to';
        $this->expectSetting($key, 'me@example.com');

        $this->assertSame([$user], $this->provider->getRecipients(['setting' => $key]));
    }

    public function testManyUserRecipients()
    {
        $user1 = $this->mockRecipient('me@example.com');
        $user2 = $this->mockRecipient('me@example.co.uk');
        $this->repo->expects($this->once())
            ->method('findByEmails')
            ->with(['me@example.com', 'me@example.co.uk'])
            ->will($this->returnValue([$user1, $user2]));
        $key = 'some_bundle.send_to';
        $this->expectSetting($key, ['me@example.com', 'me@example.co.uk']);

        $this->assertSame([$user1, $user2], $this->provider->getRecipients(['setting' => $key]));
    }

    public function testSingleNonUserRecipient()
    {
        $this->repo->expects($this->once())
            ->method('findByEmails')
            ->will($this->returnValue([]));
        $key = 'some_bundle.send_to';
        $this->expectSetting($key, 'me@example.com');

        $recipients = $this->provider->getRecipients(['setting' => $key]);
        $this->assertInstanceOf(SimpleRecipient::class, $recipients[0]);
        $this->assertSame('me@example.com', $recipients[0]->getEmail());
    }

    public function testManyNonUserRecipients()
    {
        $this->repo->expects($this->once())
            ->method('findByEmails')
            ->will($this->returnValue([]));
        $key = 'some_bundle.send_to';
        $this->expectSetting($key, ['me@example.com', 'me@example.co.uk']);

        $recipients = $this->provider->getRecipients(['setting' => $key]);
        $this->assertInstanceOf(SimpleRecipient::class, $recipients[0]);
        $this->assertSame('me@example.com', $recipients[0]->getEmail());
        $this->assertInstanceOf(SimpleRecipient::class, $recipients[1]);
        $this->assertSame('me@example.co.uk', $recipients[1]->getEmail());
    }

    public function testMixedUserAndNonUserRecipients()
    {
        $user1 = $this->mockRecipient('me@example.com');
        $user2 = $this->mockRecipient('me@example.co.uk');
        $this->repo->expects($this->once())
            ->method('findByEmails')
            ->with(['me@example.com', 'me@example.co.uk', 'me@example.org'])
            ->will($this->returnValue([$user1, $user2]));
        $key = 'some_bundle.send_to';
        $this->expectSetting($key, ['me@example.com', 'me@example.co.uk', 'me@example.org']);

        $recipients = $this->provider->getRecipients(['setting' => $key]);
        $this->assertSame($user1, $recipients[0]);
        $this->assertSame($user2, $recipients[1]);
        $this->assertInstanceOf(SimpleRecipient::class, $recipients[2]);
        $this->assertSame('me@example.org', $recipients[2]->getEmail());
    }
}
