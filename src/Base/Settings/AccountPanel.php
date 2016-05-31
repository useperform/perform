<?php

namespace Admin\Base\Settings;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * AccountPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AccountPanel implements SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder)
    {
    }

    public function getTemplate()
    {
        return 'AdminBaseBundle:Settings:account.html.twig';
    }

    public function isEnabled()
    {
        return true;
    }
}
