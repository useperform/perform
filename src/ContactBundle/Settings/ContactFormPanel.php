<?php

namespace Perform\ContactBundle\Settings;

use Perform\BaseBundle\Settings\SettingsPanelInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactFormPanel implements SettingsPanelInterface
{
    const KEY_NOTIFY_EMAIL = 'perform_contact.notify_address';

    public function buildForm(FormBuilderInterface $builder, SettingsManagerInterface $manager)
    {
        $builder->add('email', EmailType::class, [
            'data' => $manager->getValue(self::KEY_NOTIFY_EMAIL),
            'label' => 'Email address to notify',
        ]);
    }

    public function handleSubmission(FormInterface $form, SettingsManagerInterface $manager)
    {
        $manager->setValue(self::KEY_NOTIFY_EMAIL, $form->get('email')->getData());
    }

    public function getTemplate()
    {
        return '@PerformContact/settings_panel/contact_form.html.twig';
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
