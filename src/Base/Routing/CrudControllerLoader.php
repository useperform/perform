<?php

namespace Admin\Base\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;

/**
 * CrudControllerLoader creates routes dynamically for a CrudController.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudControllerLoader extends Loader
{
    protected $parser;

    public function __construct(ControllerNameParser $parser)
    {
        $this->parser = $parser;
    }

    public function load($controller, $type = null)
    {
        $class = explode(':', $this->parser->parse($controller.':_crud_'))[0];
        $refl = new \ReflectionClass($class);

        if (!$refl->isSubclassOf('Admin\Base\Controller\CrudController')) {
            throw new \InvalidArgumentException($class. ' must be an instance of Admin\Base\Controller\CrudController to use crud routing');
        }

        $collection = new RouteCollection();
        foreach ($class::getCrudActions() as $path => $action) {
            $route = new Route($path, ['_controller' => $class.'::'.$action.'Action']);
            $collection->add($this->createRouteName($class, $action), $route);
        }

        return $collection;
    }

    protected function createRouteName($class, $method)
    {
        $name = strtolower(str_replace('\\', '_', $class).'_'.$method);

        return preg_replace(['/(bundle|controller)_/', '/__/'], '_', $name);
    }

    public function supports($resource, $type = null)
    {
        return $type === 'crud';
    }
}
