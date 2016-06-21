<?php

namespace Admin\AnalyticsBundle\Panel;

use Admin\Base\Panel\PanelInterface;
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
        return $this->templating->render('AdminAnalyticsBundle:panels:hits.html.twig', []);
    }
}
