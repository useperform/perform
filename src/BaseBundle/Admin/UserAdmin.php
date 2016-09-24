<?php

namespace Perform\BaseBundle\Admin;

/**
 * UserAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserAdmin extends AbstractAdmin
{
    protected $listFields = [
        'forename',
        'surname',
    ];
    protected $viewFields = [
        'forename',
        'surname',
    ];
    protected $createFields = [
        'forename',
        'surname',
    ];
    protected $editFields = [
        'forename',
        'surname',
    ];
    protected $routePrefix = 'perform_base_user_';
}
