<?php

namespace Perform\BaseBundle\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;

/**
 * AccountPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AccountPanel implements SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder, SettingsManagerInterface $manager)
    {
    }

    public function handleSubmission(FormInterface $form, SettingsManagerInterface $manager)
    {
    }

    public function getTemplate()
    {
        return '@PerformBase/settings_panel/account.html.twig';
    }

    public function getTemplateVars()
    {
        return [];
    }

    public function isEnabled()
    {
        return true;
    }
}
