<?php

namespace Perform\BaseBundle\Admin;

use Perform\BaseBundle\Type\TypeConfig;

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
}
