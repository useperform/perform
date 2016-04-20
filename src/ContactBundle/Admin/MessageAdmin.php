<?php

namespace Admin\ContactBundle\Admin;

use Admin\Base\Admin\AbstractAdmin;

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
    ];
    protected $fieldOptions = [
        'createdAt' => [
            'type' => 'datetime',
        ]
    ];
    protected $routePrefix = 'admin_contact_message_';
}
