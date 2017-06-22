<?php

namespace Perform\BaseBundle\Routing;

use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Admin\AdminInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;

/**
 * CrudUrlGenerator.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudUrlGenerator
{
    protected $adminRegistry;
    protected $router;

    public function __construct(AdminRegistry $adminRegistry, RouterInterface $router)
    {
        $this->adminRegistry = $adminRegistry;
        $this->router = $router;
    }

    /**
     * Get the url to a crud action for an entity.
     *
     * @param string|object $entity
     * @param string        $action
     *
     * @return string
     */
    public function generate($entity, $action, array $params = [])
    {
        $params = in_array($action, ['view', 'edit']) ?
                array_merge($params, ['id' => $entity->getId()]) :
                $params;
        $admin = $this->adminRegistry->getAdmin($entity);

        return $this->router->generate($this->createRouteName($admin, $action), $params);
    }

    /**
     * Check if a crud action exists for an entity.
     *
     * @param string|object $entity
     * @param string        $action
     *
     * @return string
     */
    public function routeExists($entity, $action)
    {
        $admin = $this->adminRegistry->getAdmin($entity);

        if (!in_array($action, $admin->getActions())) {
            return false;
        }

        return $this->router->getRouteCollection()->get($this->createRouteName($admin, $action)) instanceof Route;
    }

    protected function createRouteName(AdminInterface $admin, $action)
    {
        return $admin->getRoutePrefix().strtolower(preg_replace('/([A-Z])/', '_\1', $action));
    }

    public function getDefaultEntityRoute($entity)
    {
        $admin = $this->adminRegistry->getAdmin($entity);

        $actions = $admin->getActions();

        if (in_array('list', $actions)) {
            return $admin->getRoutePrefix().'list';
        }
        if (in_array('viewDefault', $actions)) {
            return $admin->getRoutePrefix().'view_default';
        }

        throw new \Exception(sprintf('Unable to find the default route for %s', is_string($entity) ? $entity : get_class($entity)));
    }
}
