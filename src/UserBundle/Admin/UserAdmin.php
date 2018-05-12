<?php

namespace Perform\UserBundle\Admin;

use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\LabelConfig;
use Perform\UserBundle\Controller\UserController;
use Perform\BaseBundle\Admin\AbstractAdmin;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_user_user_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('forename', [
                'type' => 'string',
            ])
            ->add('surname', [
                'type' => 'string',
            ])
            ->add('email', [
                'type' => 'email',
                'listOptions' => [
                    'link' => false,
                ]
            ])
            ->add('passwordExpiresAt', [
                'type' => 'datetime',
                'contexts' => [
                    TypeConfig::CONTEXT_EDIT,
                    TypeConfig::CONTEXT_VIEW,
                ]
            ])
            ->add('plainPassword', [
                'type' => 'password',
                'options' => [
                    'label' => 'Password',
                ],
                'contexts' => [
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ]
            ])
            ;
    }

    public function configureActions(ActionConfig $config)
    {
        parent::configureActions($config);
        $config->add('perform_user_create_reset_token');
    }

    public function configureLabels(LabelConfig $config)
    {
        $config->setEntityName('User')
            ->setEntityLabel(function ($user) {
                return $user->getFullname();
            });
    }

    public function getControllerName()
    {
        return UserController::class;
    }
}
