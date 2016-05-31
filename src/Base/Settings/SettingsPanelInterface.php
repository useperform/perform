<?php

namespace Admin\Base\Settings;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * SettingsPanelInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder);

    public function getTemplate();

    public function isEnabled();
}
