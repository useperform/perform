<?php

namespace Perform\BaseBundle\Admin;

/**
 * AdminInterface.
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

    /**
     * @return array
     */
    public function getCreateFields();

    /**
     * @return array
     */
    public function getEditFields();

    /**
     * @return string
     */
    public function getFormType();

    /**
     * @return string
     */
    public function getRoutePrefix();

    /**
     * @return string
     */
    public function getControllerName();

    /**
     * @return array
     */
    public function getActions();
}
