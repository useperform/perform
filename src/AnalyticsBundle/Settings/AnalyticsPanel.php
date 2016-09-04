<?php

namespace Admin\AnalyticsBundle\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Admin\Base\Settings\SettingsManager;
use Admin\Base\Settings\SettingsPanelInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * AnalyticsPanel.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AnalyticsPanel implements SettingsPanelInterface
{
    public function buildForm(FormBuilderInterface $builder, SettingsManager $manager)
    {
        $key = 'admin_analytics_ga_key';
        $builder->add($key, TextType::class, [
            'data' => $manager->getValue($key),
            'label' => 'Google analytics key',
        ]);
    }

    public function handleSubmission(FormInterface $form, SettingsManager $manager)
    {
        $key = 'admin_analytics_ga_key';
        $manager->setValue($key, $form->get($key)->getData());
    }

    public function getTemplate()
    {
        return 'AdminAnalyticsBundle:Settings:analytics.html.twig';
    }

    public function isEnabled()
    {
        return true;
    }
}
