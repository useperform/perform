<?php

namespace Perform\BaseBundle\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;

/**
 * SettingsPanelInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder, SettingsManagerInterface $manager);

    public function handleSubmission(FormInterface $form, SettingsManagerInterface $manager);

    public function getTemplate();

    public function getTemplateVars();

    public function isEnabled();
}
