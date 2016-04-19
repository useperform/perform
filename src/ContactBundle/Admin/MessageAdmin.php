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
        // 'timeSent',
    ];
    protected $viewFields = [
        'name',
        'email',
        // 'timeSent',
    ];
    protected $routePrefix = 'admin_contact_message_';
}
