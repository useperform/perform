<?php

namespace Perform\ContactBundle\Settings;

use Perform\BaseBundle\Settings\SettingsPanelInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Settings\SettingsManager;

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
        $key = 'perform_contact_notify_address';
        $builder->add($key, TextType::class, [
            'data' => $manager->getValue($key),
            'label' => 'Email address to notify',
        ]);
    }

    public function handleSubmission(FormInterface $form, SettingsManager $manager)
    {
        $key = 'perform_contact_notify_address';
        $manager->setValue($key, $form->get($key)->getData());
    }

    public function getTemplate()
    {
        return 'PerformContactBundle:Settings:contactForm.html.twig';
    }

    public function isEnabled()
    {
        return true;
    }
}
