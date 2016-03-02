<?php

namespace Admin\Base\Admin;

use Symfony\Component\Form\FormBuilderInterface;

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
     * @param FormBuilderInterface $builder
     * @param mixed                $entity
     */
    public function buildCreateForm(FormBuilderInterface $builder, $entity);
}
