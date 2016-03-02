<?php

namespace Admin\Base\Admin;

/**
 * UserAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserAdmin implements AdminInterface
{
    protected $listFields = [
        'forename',
        'surname',
    ];

    public function getListFields()
    {
        return $this->listFields;
    }
}
