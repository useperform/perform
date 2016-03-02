<?php

namespace Admin\Base\Admin;

/**
 * AdminInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface AdminInterface
{
    /**
     * @return array
     */
    public function getListFields();

    /**
     * @return array
     */
    public function getViewFields();
}
