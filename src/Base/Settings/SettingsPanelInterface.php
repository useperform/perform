<?php

namespace Perform\Base\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Perform\Base\Settings\SettingsManager;

/**
 * SettingsPanelInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder, SettingsManager $manager);

    public function handleSubmission(FormInterface $form, SettingsManager $manager);

    public function getTemplate();

    public function isEnabled();
}
