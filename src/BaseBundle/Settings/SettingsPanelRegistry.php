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
     * @return string
     */
    public function renderBlock($panelName, $blockName, array $vars = [])
    {
        $panel = $this->getPanel($panelName);
        $template = $this->twig->loadTemplate($panel->getTemplate());
        $vars = array_merge($vars, $panel->getTemplateVars());

        return $template->renderBlock($blockName, $vars);
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
