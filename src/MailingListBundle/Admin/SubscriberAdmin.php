<?php

namespace Perform\MailingListBundle\Admin;

use Perform\Base\Admin\AbstractAdmin;

/**
 * SubscriberAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberAdmin extends AbstractAdmin
{
    protected $listFields = [
        'fullname',
        'email',
        'createdAt',
    ];
    protected $viewFields = [
        'forename',
        'surname',
        'email',
        'createdAt',
    ];
    protected $createFields = [
        'forename',
        'surname',
        'email',
    ];
    protected $editFields = [
        'forename',
        'surname',
        'email',
    ];
    protected $fieldOptions = [
        'createdAt' => [
            'type' => 'datetime',
            'label' => 'Sign-up date',
        ],
    ];
    protected $routePrefix = 'admin_mailing_list_subscriber_';
}
