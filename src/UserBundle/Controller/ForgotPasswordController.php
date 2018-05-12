<?php

namespace Perform\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Perform\UserBundle\Form\Type\ForgotPasswordType;
use Perform\UserBundle\Form\Type\ResetPasswordType;
use Symfony\Component\Form\FormError;
use Perform\UserBundle\Security\ResetTokenException;
use Perform\UserBundle\Security\ResetTokenManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ForgotPasswordController extends Controller
{
    /**
     * @Template()
     */
    public function forgot(ResetTokenManager $manager, Request $request)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $token = $manager->createAndSaveToken($form->get('email')->getData());
                $manager->sendNotification($token);

                $this->addFlash('perform_password_success', 'You should receive an email with a link to reset your password shortly.');

                return $this->redirectToRoute('perform_user_forgot_password_success');
            } catch (ResetTokenException $e) {
                $form->get('email')->addError(new FormError($e->getMessage()));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     */
    public function reset(Request $request)
    {
        try {
            $manager = $this->get('perform_user.reset_token_manager');
            $token = $manager->findAndValidateToken(
                $request->query->get('id'),
                $request->query->get('secret')
            );

            $user = $token->getUser();
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $manager->updatePassword($token, $form->get('password')->getData());

                $this->addFlash('perform_password_success', 'Your password has been reset.');

                return $this->redirectToRoute('perform_user_forgot_password_success');
            }

            return [
                'form' => $form->createView(),
                'user' => $user,
            ];
        } catch (ResetTokenException $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @Template
     */
    public function success(Request $request)
    {
        $flashes = $request->getSession()->getFlashBag()->get('perform_password_success', []);
        if (!isset($flashes[0])) {
            throw new AccessDeniedException();
        }

        return [
            'message' => $flashes[0],
        ];
    }
}
