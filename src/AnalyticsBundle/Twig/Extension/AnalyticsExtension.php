<?php

namespace Perform\AnalyticsBundle\Twig\Extension;

use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AnalyticsExtension extends \Twig_Extension
{
    protected $settings;
    protected $vendors;

    public function __construct(SettingsManagerInterface $settings, array $vendors = [])
    {
        $this->settings = $settings;
        $this->vendors = $vendors;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_analytics', [$this, 'getTrackingCode'], ['is_safe' => ['html']]),
        ];
    }

    public function getTrackingCode()
    {
        if (!$this->settings->getValue('perform_analytics.enabled', false)) {
            return sprintf('<!-- disabled: analytics for %s -->', implode(', ', $this->vendors));
        }

        $html = '';
        if (in_array('google', $this->vendors)) {
            $html .= $this->getGoogleAnalyticsCode();
        }

        return $html;
    }

    protected function getGoogleAnalyticsCode()
    {
        $script = <<<EOF
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                         m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', '%s', 'auto');
ga('send', 'pageview');
</script>
EOF;

        return sprintf($script, $this->settings->getRequiredValue('perform_analytics.ga_key'));
    }

    public function getName()
    {
        return 'analytics';
    }
}
