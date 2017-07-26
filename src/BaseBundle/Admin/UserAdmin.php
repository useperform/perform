<?php

namespace Perform\BaseBundle\Admin;

use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Controller\UserController;
use Perform\BaseBundle\Config\LabelConfig;

/**
 * UserAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_base_user_';

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
        $config->add('perform_base_create_reset_token');
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
