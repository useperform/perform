<?php

namespace Perform\Team\Admin;

use Perform\Base\Admin\AbstractAdmin;

/**
 * TeamMemberAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TeamMemberAdmin extends AbstractAdmin
{
    protected $listFields = [
        'name',
        'role',
    ];
    protected $viewFields = [
        'name',
        'role',
    ];
    protected $createFields = [
        'name',
        'role',
        'image',
        'description',
    ];
    protected $editFields = [
        'name',
        'role',
        'image',
        'description',
    ];
    protected $fieldOptions = [
        'description' => [
            'type' => 'text',
        ],
        'image' => [
            'type' => 'image',
        ],
    ];
    protected $routePrefix = 'admin_team_team_';
}
