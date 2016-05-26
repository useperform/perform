<?php

namespace Admin\Base\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Admin\Base\Admin\AdminRegistry;
use Symfony\Component\Config\Resource\FileResource;

/**
 * CrudLoader creates crud routes dynamically for an entity admin.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudLoader extends Loader
{
    protected $registry;

    public function __construct(AdminRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function load($entity, $type = null)
    {
        $admin = $this->registry->getAdmin($entity);
        $class = $admin->getControllerName();
        $refl = new \ReflectionClass($class);

        $crudClass = 'Admin\Base\Controller\CrudController';
        if ($refl->getName() !== $crudClass && !$refl->isSubclassOf($crudClass)) {
            throw new \InvalidArgumentException($class.' must be an instance of Admin\Base\Controller\CrudController to use crud routing');
        }

        $collection = new RouteCollection();
        foreach ($admin->getActions() as $path => $action) {
            $route = new Route($path, [
                '_controller' => $class.'::'.$action.'Action',
                '_entity' => $entity,
            ]);
            $collection->add($admin->getRoutePrefix().$action, $route);
        }
        $adminRefl = new \ReflectionClass($admin);
        $collection->addResource(new FileResource($adminRefl->getFileName()));

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'crud';
    }
}
