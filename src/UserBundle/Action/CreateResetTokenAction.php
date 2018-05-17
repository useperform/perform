<?php

namespace Perform\UserBundle\Action;

use Perform\UserBundle\Security\ResetTokenManager;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Action\ActionResponse;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateResetTokenAction implements ActionInterface
{
    protected $tokenManager;

    public function __construct(ResetTokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function run(array $entities, array $options)
    {
        foreach ($entities as $user) {
            $this->tokenManager->createAndSaveToken($user->getEmail());
        }

        $msg = count($entities) === 1 ?
             sprintf('Created new reset token for %s.', $entities[0]->getEmail()) :
             sprintf('Created new reset tokens for %s users.', count($entities));
        $response = new ActionResponse($msg);
        $response->setRedirect(ActionResponse::REDIRECT_CURRENT);

        return $response;
    }

    public function getDefaultConfig()
    {
        return [
            'label' => 'Create reset token',
            'isButtonAvailable' => function ($user, CrudRequest $request) {
                return $request->getContext() === 'view';
            },
            'buttonStyle' => 'btn-light',
        ];
    }
}
