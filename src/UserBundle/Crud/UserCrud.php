<?php

namespace Perform\UserBundle\Crud;

use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\LabelConfig;
use Perform\UserBundle\Controller\UserController;
use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserCrud extends AbstractCrud
{
    public function configureFields(FieldConfig $config)
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
                ],
            ])
            ->add('passwordExpiresAt', [
                'type' => 'datetime',
                'contexts' => [
                    CrudRequest::CONTEXT_EDIT,
                    CrudRequest::CONTEXT_VIEW,
                ],
            ])
            ->add('plainPassword', [
                'type' => 'password',
                'options' => [
                    'label' => 'Password',
                ],
                'contexts' => [
                    CrudRequest::CONTEXT_CREATE,
                    CrudRequest::CONTEXT_EDIT,
                ],
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
