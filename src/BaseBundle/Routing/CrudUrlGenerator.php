<?php

namespace Perform\BaseBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudUrlGenerator implements CrudUrlGeneratorInterface
{
    protected $generator;
    protected $routeOptions;

    public function __construct(UrlGeneratorInterface $generator, array $routeOptions = [])
    {
        $this->generator = $generator;
        $this->routeOptions = $routeOptions;
    }

    public function generate($crudName, $context, array $params = [])
    {
        $crudName = (string) $crudName;

        if (in_array($context, ['view', 'edit'])) {
            if (!isset($params['entity'])) {
                throw new \InvalidArgumentException(sprintf('Missing required "entity" parameter to generate a crud route for "%s", context "%s".', $crudName, $context));
            }

            $params = array_merge($params, ['id' => $params['entity']->getId()]);
            unset($params['entity']);
        }

        return $this->generator->generate($this->getRouteName($crudName, $context), $params);
    }

    public function routeExists($crudName, $context)
    {
        try {
            $this->generator->generate($this->getRouteName($crudName, $context));

            return true;
        } catch (RouteNotFoundException $e) {
            // thrown by url generator when the route isn't found, or by getRouteName() if the crud isn't available at all
            return false;
        } catch (MissingMandatoryParametersException $e) {
            // missing parameters, but route exists
            return true;
        }
    }

    public function getRouteName($crudName, $context)
    {
        if (!isset($this->routeOptions[$crudName]['route_name_prefix'])) {
            throw new RouteNotFoundException(sprintf('No routes have been registered for the crud name "%s".', $crudName));
        }

        return $this->routeOptions[$crudName]['route_name_prefix'].$context;
    }
}
