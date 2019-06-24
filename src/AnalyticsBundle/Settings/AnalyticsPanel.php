<?php

namespace Perform\AnalyticsBundle\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Perform\BaseBundle\Settings\SettingsPanelInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AnalyticsPanel implements SettingsPanelInterface
{
    const KEY_ENABLED = 'perform_analytics.enabled';
    const KEY_GOOGLE = 'perform_analytics.ga_key';

    protected $manager;
    protected $canEnable;
    protected $vendors;

    public function __construct(SettingsManagerInterface $manager, $canEnable, array $vendors = [])
    {
        // need to inject this to use it in getTemplateVars()
        $this->manager = $manager;
        $this->canEnable = $canEnable;
        $this->vendors = $vendors;
    }

    public function buildForm(FormBuilderInterface $builder, SettingsManagerInterface $manager)
    {
        if ($this->canEnable) {
            $builder->add('enabled', CheckboxType::class, [
                'data' => $manager->getValue(self::KEY_ENABLED),
                'label' => 'Enabled',
                'required' => false,
            ]);
        }

        if (in_array('google', $this->vendors)) {
            $builder->add('google', TextType::class, [
                'data' => $manager->getValue(self::KEY_GOOGLE),
                'label' => 'Google analytics key',
                'required' => false,
            ]);
        }
    }

    public function handleSubmission(FormInterface $form, SettingsManagerInterface $manager)
    {
        if ($this->canEnable) {
            $manager->setValue(self::KEY_ENABLED, $form->get('enabled')->getData());
        }

        if (in_array('google', $this->vendors)) {
            $manager->setValue(self::KEY_GOOGLE, $form->get('google')->getData());
        }
    }

    public function getTemplate()
    {
        return '@PerformAnalytics/settings_panel/analytics.html.twig';
    }

    public function getTemplateVars()
    {
        return [
            'enabled' => $this->manager->getValue(self::KEY_ENABLED, false),
            'canEnable' => $this->canEnable,
            'vendors' => $this->vendors,
        ];
    }

    public function isEnabled()
    {
        return true;
    }
}
