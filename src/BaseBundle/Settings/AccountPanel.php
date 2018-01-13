<?php

namespace Perform\BaseBundle\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Settings\SettingsManager;

/**
 * AccountPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AccountPanel implements SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder, SettingsManager $manager)
    {
    }

    public function handleSubmission(FormInterface $form, SettingsManager $manager)
    {
    }

    public function getTemplate()
    {
        return '@PerformBase/settings/account.html.twig';
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
