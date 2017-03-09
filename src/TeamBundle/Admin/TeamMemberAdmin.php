<?php

namespace Perform\TeamBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * TeamMemberAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TeamMemberAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_team_team_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('name', [
                'type' => 'string',
            ])
            ->add('role', [
                'type' => 'string',
            ])
            ->add('image', [
                'type' => 'media',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
                'options' => [
                    'types' => 'image',
                ]
            ])
            ->add('description', [
                'type' => 'text',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
            ])
            ;
    }
}
