<?php

namespace Perform\BaseBundle\Routing;

use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Crud\CrudInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Perform\BaseBundle\Crud\CrudNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudUrlGenerator implements CrudUrlGeneratorInterface
{
    protected $crudRegistry;
    protected $router;

    public function __construct(CrudRegistry $crudRegistry, RouterInterface $router)
    {
        $this->crudRegistry = $crudRegistry;
        $this->router = $router;
    }

    public function generate($entity, $action, array $params = [])
    {
        $params = in_array($action, ['view', 'edit']) ?
                array_merge($params, ['id' => $entity->getId()]) :
                $params;
        $crud = $this->crudRegistry->getCrud($entity);

        return $this->router->generate($this->createRouteName($crud, $action), $params);
    }

    /**
     * @return bool
     */
    public function routeExists($entity, $action)
    {
        try {
            $crud = $this->crudRegistry->getCrud($entity);
        } catch (CrudNotFoundException $e) {
            return false;
        }

        if (!in_array($action, $crud->getActions())) {
            return false;
        }

        return $this->router->getRouteCollection()->get($this->createRouteName($crud, $action)) instanceof Route;
    }

    protected function createRouteName(CrudInterface $crud, $action)
    {
        return $crud->getRoutePrefix().strtolower(preg_replace('/([A-Z])/', '_\1', $action));
    }

    public function generateDefaultEntityRoute($entity)
    {
        return $this->router->generate($this->getDefaultEntityRoute($entity));
    }

    public function getDefaultEntityRoute($entity)
    {
        $crud = $this->crudRegistry->getCrud($entity);

        $actions = $crud->getActions();

        if (in_array('list', $actions)) {
            return $crud->getRoutePrefix().'list';
        }
        if (in_array('viewDefault', $actions)) {
            return $crud->getRoutePrefix().'view_default';
        }

        throw new \Exception(sprintf('Unable to find the default route for %s', is_string($entity) ? $entity : get_class($entity)));
    }
}
