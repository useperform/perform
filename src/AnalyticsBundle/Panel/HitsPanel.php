<?php

namespace Perform\AnalyticsBundle\Panel;

use Perform\BaseBundle\Panel\PanelInterface;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HitsPanel implements PanelInterface
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render()
    {
        return $this->twig->render('@PerformAnalytics/panel/hits.html.twig', []);
    }
}
