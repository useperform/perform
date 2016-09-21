<?php

namespace Perform\ContactBundle\Admin;

use Perform\Base\Admin\AbstractAdmin;

/**
 * MessageAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessageAdmin extends AbstractAdmin
{
    protected $listFields = [
        'name',
        'email',
        'createdAt',
    ];
    protected $viewFields = [
        'name',
        'email',
        'createdAt',
        'message',
    ];
    protected $fieldOptions = [
        'createdAt' => [
            'type' => 'datetime',
        ]
    ];
    protected $routePrefix = 'admin_contact_message_';

    public function getActions()
    {
        return [
            '/' => 'list',
            '/view/{id}' => 'view',
        ];
    }
}
