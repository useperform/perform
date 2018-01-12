<?php

namespace Perform\UserBundle\Tests\EventListener;

use Perform\UserBundle\EventListener\LoginListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\UserBundle\Entity\User;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Perform\UserBundle\Event\FirstLoginEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoginListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->listener = new LoginListener($this->em);
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->session = $this->getMock(SessionInterface::class);
    }

    public function testFirstLogin()
    {
        $user = new User();
        $request = new Request();
        $request->setSession($this->session);
        $this->session->expects($this->once())
            ->method('set')
            ->with(LoginListener::FIRST_LOGIN, true);
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(LoginListener::FIRST_LOGIN, $this->callback(function ($event) use ($user) {
                return $event instanceof FirstLoginEvent && $event->getUser() === $user;
            }));
        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->em->expects($this->once())
            ->method('flush');

        $this->listener->onLogin(new InteractiveLoginEvent($request, new UsernamePasswordToken($user, [], 1)), 'security.interactive_login', $this->dispatcher);
        $this->assertNotNull($user->getLastLogin());
    }

    public function testReturningLogin()
    {
        $user = new User();
        $user->setLastLogin(new \DateTime());
        $request = new Request();
        $request->setSession($this->session);
        $this->session->expects($this->never())
            ->method('set');
        $this->dispatcher->expects($this->never())
            ->method('dispatch');
        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->em->expects($this->once())
            ->method('flush');

        $this->listener->onLogin(new InteractiveLoginEvent($request, new UsernamePasswordToken($user, [], 1)), 'security.interactive_login', $this->dispatcher);
    }
}
