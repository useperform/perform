<?php

namespace Perform\BaseBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Perform\BaseBundle\Crud\CrudRegistry;
use Symfony\Component\Config\Resource\FileResource;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Controller\CrudController;

/**
 * CrudLoader creates crud routes dynamically for an entity crud.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudLoader extends Loader
{
    protected $registry;
    protected $routeOptions;

    public function __construct(CrudRegistry $registry, array $routeOptions = [])
    {
        $this->registry = $registry;
        $this->routeOptions = $routeOptions;
    }

    public function load($crudName, $type = null)
    {
        $crud = $this->registry->get($crudName);

        $controllerClass = $crud->getControllerName();
        $refl = new \ReflectionClass($controllerClass);
        $baseControllerClass = CrudController::class;
        if ($refl->getName() !== $baseControllerClass && !$refl->isSubclassOf($baseControllerClass)) {
            throw new \InvalidArgumentException(sprintf('%s must be an instance of %s to use crud routing.', $controllerClass, $baseControllerClass));
        }

        $options = $this->getOptions($crudName);
        $collection = new RouteCollection();
        foreach ($options['contexts'] as $context => $urlFragment) {
            $route = new Route($urlFragment, [
                '_controller' => $controllerClass.'::'.$context.'Action',
                '_crud' => $crudName,
            ]);
            $collection->add($options['route_name_prefix'].$context, $route);
        }
        $crudRefl = new \ReflectionClass($crud);
        $filename = $crudRefl->getFileName();
        try {
            $collection->addResource(new FileResource($filename));
        } catch (\InvalidArgumentException $e) {
            //file doesn't exist, do nothing
        }

        return $collection;
    }

    protected function getOptions($crudName)
    {
        if (!isset($this->routeOptions[$crudName])) {
            throw new \InvalidArgumentException(sprintf('Unable to register routes for the given crud "%s", route options have not been registered.', $crudName));
        }

        return $this->routeOptions[$crudName];
    }

    public function supports($resource, $type = null)
    {
        return $type === 'crud';
    }
}
