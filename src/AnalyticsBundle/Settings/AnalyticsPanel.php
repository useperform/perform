<?php

namespace Perform\AnalyticsBundle\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Settings\SettingsManager;
use Perform\BaseBundle\Settings\SettingsPanelInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * AnalyticsPanel.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AnalyticsPanel implements SettingsPanelInterface
{
    protected $enabled;
    protected $vendors;

    public function __construct($enabled, array $vendors = [])
    {
        $this->enabled = (bool) $enabled;
        $this->vendors = $vendors;
    }

    public function buildForm(FormBuilderInterface $builder, SettingsManager $manager)
    {
        if (in_array('google', $this->vendors)) {
            $key = 'perform_analytics_ga_key';
            $builder->add($key, TextType::class, [
                'data' => $manager->getValue($key),
                'label' => 'Google analytics key',
            ]);
        }
    }

    public function handleSubmission(FormInterface $form, SettingsManager $manager)
    {
        if (in_array('google', $this->vendors)) {
            $key = 'perform_analytics_ga_key';
            $manager->setValue($key, $form->get($key)->getData());
        }
    }

    public function getTemplate()
    {
        return '@PerformAnalytics/settings/analytics.html.twig';
    }

    public function getTemplateVars()
    {
        return [
            'enabled' => $this->enabled,
        ];
    }

    public function isEnabled()
    {
        return true;
    }
}
