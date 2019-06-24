<?php

namespace Perform\UserBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Perform\UserBundle\Security\ResetTokenManager;
use Perform\UserBundle\Entity\User;
use Perform\UserBundle\Entity\ResetToken;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Perform\NotificationBundle\Notifier\NotifierInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\UserBundle\Security\UserManager;

/**
 * ResetTokenManagerTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ResetTokenManagerTest extends TestCase
{
    protected $em;
    protected $notifier;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->userManager = $this->getMockBuilder(UserManager::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $this->resolver = $this->createMock(EntityResolver::class);
        $this->repo = $this->createMock(ObjectRepository::class);

        $this->resolver->expects($this->any())
            ->method('resolve')
            ->with('PerformUserBundle:User')
            ->will($this->returnValue('Perform\UserBundle\Entity\User'));
        $this->em->expects($this->any())
            ->method('getRepository')
            ->with('Perform\UserBundle\Entity\User')
            ->will($this->returnValue($this->repo));

        $this->notifier = $this->createMock(NotifierInterface::class);

        $this->manager = new ResetTokenManager($this->em, $this->userManager, $this->resolver, $this->notifier, 1800);
    }

    public function testCreateToken()
    {
        $user = new User();
        $token = $this->manager->createToken($user);
        $this->assertInstanceOf(ResetToken::class, $token);
        $this->assertSame($user, $token->getUser());
        $this->assertNotNull($token->getSecret());
        $this->assertInstanceOf(\DateTime::class, $token->getExpiresAt());
    }

    public function testCreateAndSaveToken()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $this->repo->expects($this->any())
            ->method('findOneBy')
            ->with(['email' => 'test@example.com'])
            ->will($this->returnValue($user));

        $token = $this->manager->createAndSaveToken($user->getEmail());
        $this->assertInstanceOf(ResetToken::class, $token);
        $this->assertSame($user, $token->getUser());
    }

    public function testIsTokenValid()
    {
        $token = new ResetToken();
        $token->setSecret('foo');
        $token->setExpiresAt(new \DateTime('tomorrow'));
        $secret = 'foo';

        $this->assertTrue($this->manager->isTokenValid($token, $secret));
    }

    public function testUpdatePassword()
    {
        $token = new ResetToken();
        $user = new User();
        $token->setUser($user);

        $this->userManager->expects($this->once())
            ->method('updatePassword')
            ->with($user, 'hunter2');

        $this->em->expects($this->once())
            ->method('remove')
            ->with($token);
        $this->em->expects($this->once())
            ->method('flush');

        $this->manager->updatePassword($token, 'hunter2');
    }

    public function testExpiryTimeCanBeConfigured()
    {
        $manager = new ResetTokenManager($this->em, $this->userManager, $this->resolver, $this->notifier, 3600);
        $user = new User();
        $token = $manager->createToken($user);
        $this->assertInstanceOf(\DateTime::class, $token->getExpiresAt());
        $expected = new \DateTime();
        $expected->modify('+3600 seconds');
        $this->assertEquals($expected, $token->getExpiresAt(), '', 1);
    }
}
