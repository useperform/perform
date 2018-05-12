<?php

namespace Perform\UserBundle\Tests\EventListener;

use Perform\UserBundle\Entity\User;
use Perform\UserBundle\EventListener\NewPasswordListener;
use Perform\UserBundle\Form\Type\ResetPasswordType;
use Perform\UserBundle\Security\UserManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NewPasswordListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $tokenStorage;
    protected $userManager;
    protected $form;
    protected $passwordField;
    protected $twig;

    public function setUp()
    {
        $this->tokenStorage = $this->getMock(TokenStorageInterface::class);
        $this->token = $this->getMock(TokenInterface::class);
        $this->tokenStorage->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->token));

        $this->userManager = $this->getMockBuilder(UserManager::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->form = $this->getMock(FormInterface::class);
        $this->passwordField = $this->getMock(FormInterface::class);
        $this->form->expects($this->any())
            ->method('get')
            ->with('password')
            ->will($this->returnValue($this->passwordField));

        $formFactory = $this->getMock(FormFactoryInterface::class);
        $formFactory->expects($this->any())
            ->method('create')
            ->with(ResetPasswordType::class)
            ->will($this->returnValue($this->form));

        $this->twig = $this->getMockBuilder(Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->listener = new NewPasswordListener($this->tokenStorage, $this->userManager, $formFactory, $this->twig);
    }

    private function newEvent()
    {
        $kernel = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/target-url');

        return new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);
    }

    private function setUser(User $user)
    {
        $this->token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));
    }

    public function testNotLoggedIn()
    {
        $event = $this->newEvent();
        $this->listener->onKernelRequest($event);
        $this->assertNull($event->getResponse());
    }

    public function testLoggedInWithValidPassword()
    {
        $event = $this->newEvent();
        $user = new User();
        $user->setPasswordExpiresAt(new \DateTime('+1 day'));
        $this->setUser($user);
        $this->listener->onKernelRequest($event);
        $this->assertNull($event->getResponse());
    }

    public function testLoggedInWithExpiredPassword()
    {
        $event = $this->newEvent();
        $user = new User();
        $user->setPasswordExpiresAt(new \DateTime('-1 day'));
        $this->setUser($user);
        $this->twig->expects($this->once())
            ->method('render')
            ->with('@PerformUser/require_new_password/change.html.twig')
            ->will($this->returnValue('form_template'));

        $this->listener->onKernelRequest($event);
        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('form_template', $response->getContent());
    }

    public function testLoggedInSubmittingTheForm()
    {
        $event = $this->newEvent();
        $user = new User();
        $user->setPasswordExpiresAt(new \DateTime('-1 day'));
        $this->setUser($user);
        $this->form->expects($this->any())
            ->method('isSubmitted')
            ->will($this->returnValue(true));
        $this->form->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        $this->passwordField->expects($this->any())
            ->method('getData')
            ->will($this->returnValue('new password'));

        $this->twig->expects($this->never())
            ->method('render');
        $this->userManager->expects($this->once())
            ->method('updatePassword')
            ->with($user, 'new password');

        $this->listener->onKernelRequest($event);
        $response = $event->getResponse();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/target-url', $response->getTargetUrl());
    }
}
