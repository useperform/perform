<?php

namespace Perform\UserBundle\EventListener;

use Perform\UserBundle\Entity\User;
use Perform\UserBundle\Form\Type\ResetPasswordType;
use Perform\UserBundle\Security\UserManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force a logged in user to reset their password if it has expired.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NewPasswordListener
{
    protected $tokenStorage;
    protected $userManager;
    protected $formFactory;
    protected $twig;
    protected $routeName;

    public function __construct(TokenStorageInterface $tokenStorage, UserManager $userManager, FormFactoryInterface $formFactory, Environment $twig)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        foreach ($token->getRoles() as $role) {
            if ($role->getRole() === 'ROLE_PREVIOUS_ADMIN') {
                return;
            }
        }

        $user = $token->getUser();
        if (!$user instanceof User || !$user->isPasswordExpired()) {
            return;
        }

        $this->handlePasswordChange($user, $event);
    }

    /**
     * Handle the user password change.
     *
     * This method acts as a controller, but without registering a
     * route that may be in a different firewall, which would cause
     * complications.
     */
    protected function handlePasswordChange(User $user, GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $form = $this->formFactory->create(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updatePassword($user, $form->get('password')->getData());

            // password updated, redirect to the originally requested URL
            $event->setResponse(new RedirectResponse($request->getPathInfo()));
            return;
        }

        $response = new Response($this->twig->render('@PerformUser/require_new_password/change.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]));
        $event->setResponse($response);
    }
}
