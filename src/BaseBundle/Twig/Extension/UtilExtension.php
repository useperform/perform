<?php

namespace Perform\BaseBundle\Twig\Extension;

use Carbon\Carbon;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * General twig helpers.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtension extends \Twig_Extension
{
    protected $router;
    protected $configStore;

    public function __construct(RouterInterface $router, ConfigStoreInterface $configStore)
    {
        $this->router = $router;
        $this->configStore = $configStore;
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
            new \Twig_SimpleFunction('perform_entity_label', [$this, 'entityLabel']),
            new \Twig_SimpleFunction('perform_entity_name', [$this, 'entityName']),
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

    public function entityName($entity)
    {
        return $this->configStore->getLabelConfig($entity)->getEntityName();
    }

    public function entityLabel($entity)
    {
        return $this->configStore->getLabelConfig($entity)->getEntityLabel($entity);
    }

    public function getName()
    {
        return 'perform_base_util';
    }
}
