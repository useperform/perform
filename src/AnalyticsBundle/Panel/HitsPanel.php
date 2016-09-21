<?php

namespace Perform\AnalyticsBundle\Panel;

use Perform\Base\Panel\PanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * HitsPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HitsPanel implements PanelInterface
{
    protected $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function render()
    {
        return $this->templating->render('PerformAnalyticsBundle:panels:hits.html.twig', []);
    }
}
