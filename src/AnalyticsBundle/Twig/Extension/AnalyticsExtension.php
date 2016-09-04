<?php

namespace Admin\AnalyticsBundle\Twig\Extension;

use Admin\Base\Settings\SettingsManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AnalyticsExtension extends \Twig_Extension
{
    protected $settings;

    public function __construct(SettingsManager $settings)
    {
        $this->settings = $settings;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('analytics_tracking', [$this, 'getTrackingCode'], ['is_safe' => ['html']]),
        ];
    }

    public function getTrackingCode()
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

        return sprintf($script, $this->settings->getValue('admin_analytics_ga_key'));
    }

    public function getName()
    {
        return 'analytics';
    }
}
