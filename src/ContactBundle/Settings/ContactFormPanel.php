<?php

namespace Admin\ContactBundle\Settings;

use Admin\Base\Settings\SettingsPanelInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Admin\Base\Settings\SettingsManager;

/**
 * ContactFormPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactFormPanel implements SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder, SettingsManager $manager)
    {
        //auto add??
        $key = 'admin_contact_contact_form_email';
        $builder->add($key, TextType::class, [
            'data' => $manager->getValue($key),
        ]);
    }

    public function handleSubmission(FormInterface $form, SettingsManager $manager)
    {
        $key = 'admin_contact_contact_form_email';
        $manager->setValue($key, $form->get($key)->getData());
    }

    public function getTemplate()
    {
        return 'AdminContactBundle:Settings:contactForm.html.twig';
    }

    public function isEnabled()
    {
        return true;
    }
}
