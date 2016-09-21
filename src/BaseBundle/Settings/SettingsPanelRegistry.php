<?php

namespace Perform\BaseBundle\Settings;

/**
 * SettingsPanelRegistry.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsPanelRegistry
{
    protected $twig;
    protected $panels;

    public function __construct(\Twig_Environment $twig, array $panels)
    {
        $this->twig = $twig;
        $this->panels = $panels;
    }

    /**
     * @return Twig_TemplateInterface
     */
    public function getTemplate($name)
    {
        return $this->twig->loadTemplate($this->getPanel($name)->getTemplate());
    }

    public function getPanel($name)
    {
        if (!isset($this->panels[$name])) {
            throw new \InvalidArgumentException(sprintf('Unknown settings panel "%s"', $name));
        }

        return $this->panels[$name];
    }

    public function getEnabledPanels()
    {
        return array_keys(array_filter($this->panels, function ($panel) {
            return $panel->isEnabled();
        }));
    }
}
