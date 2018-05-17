<?php

namespace Perform\BaseBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Perform\BaseBundle\Crud\CrudRegistry;
use Symfony\Component\Config\Resource\FileResource;
use Perform\BaseBundle\Crud\CrudInterface;

/**
 * CrudLoader creates crud routes dynamically for an entity crud.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudLoader extends Loader
{
    protected $registry;

    public function __construct(CrudRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function load($entity, $type = null)
    {
        $crud = $this->registry->getCrud($entity);
        $class = $crud->getControllerName();
        $refl = new \ReflectionClass($class);

        $crudClass = 'Perform\BaseBundle\Controller\CrudController';
        if ($refl->getName() !== $crudClass && !$refl->isSubclassOf($crudClass)) {
            throw new \InvalidArgumentException($class.' must be an instance of Perform\BaseBundle\Controller\CrudController to use crud routing');
        }

        $collection = new RouteCollection();
        foreach ($crud->getActions() as $path => $action) {
            $route = new Route($path, [
                '_controller' => $class.'::'.$action.'Action',
                '_entity' => $entity,
            ]);
            $collection->add($this->createRouteName($crud, $action), $route);
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

    protected function createRouteName(CrudInterface $crud, $action)
    {
        return $crud->getRoutePrefix().strtolower(preg_replace('/([A-Z])/', '_\1', $action));
    }

    public function supports($resource, $type = null)
    {
        return $type === 'crud';
    }
}
