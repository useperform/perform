<?php

namespace Perform\BaseBundle\Twig\Extension;

use Carbon\Carbon;
use Perform\BaseBundle\Routing\RouteChecker;

/**
 * General twig helpers.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtension extends \Twig_Extension
{
    protected $routeChecker;

    public function __construct(RouteChecker $routeChecker)
    {
        $this->routeChecker = $routeChecker;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('perform_human_date', [$this, 'humanDate']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_route_exists', [$this->routeChecker, 'routeExists']),
        ];
    }

    public function humanDate(\DateTime $date = null)
    {
        if (!$date) {
            return '';
        }

        return Carbon::instance($date)->diffForHumans();
    }

    public function getName()
    {
        return 'perform_base_util';
    }
}
