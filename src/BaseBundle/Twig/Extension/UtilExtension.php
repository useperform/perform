<?php

namespace Perform\BaseBundle\Twig\Extension;

use Carbon\Carbon;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * General twig helpers.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtension extends \Twig_Extension
{
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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
        try {
            $this->urlGenerator->generate($routeName);

            return true;
        } catch (RouteNotFoundException $e) {
            return false;
        } catch (MissingMandatoryParametersException $e) {
            // missing parameters, but route exists
            return true;
        }
    }

    public function getName()
    {
        return 'perform_base_util';
    }
}
