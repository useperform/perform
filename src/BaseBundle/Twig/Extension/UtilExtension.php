<?php

namespace Perform\BaseBundle\Twig\Extension;

use Carbon\Carbon;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;

/**
 * General twig helpers.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtension extends \Twig_Extension
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
            new \Twig_SimpleFunction('perform_route_exists', [$this, 'routeExists']),
        ];
    }

    public function humanDate(\DateTime $date = null)
    {
        if (!$date) {
            return '';
        }

        return Carbon::instance($date)->diffForHumans();
    }

    public function routeExists($routeName)
    {
        return $this->router->getRouteCollection()->get($routeName) instanceof Route;
    }

    public function getName()
    {
        return 'perform_base_util';
    }
}
