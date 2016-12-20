<?php

namespace Perform\BaseBundle\Routing;

use Perform\BaseBundle\Admin\AdminRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Perform\BaseBundle\Admin\AdminInterface;

/**
 * CrudUrlGenerator
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudUrlGenerator
{
    protected $adminRegistry;
    protected $urlGenerator;

    public function __construct(AdminRegistry $adminRegistry, UrlGeneratorInterface $urlGenerator)
    {
        $this->adminRegistry = $adminRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Get the url to a crud action for an entity.
     *
     * @param string|object $entity
     * @param string $action
     *
     * @return string
     */
    public function generate($entity, $action, array $params = [])
    {
        $params = in_array($action, ['view', 'edit', 'delete']) ?
                array_merge($params, ['id' => $entity->getId()]) :
                $params;
        $admin = is_string($entity) ?
               $this->adminRegistry->getAdmin($entity) :
               $this->adminRegistry->getAdminForEntity($entity);

        return $this->urlGenerator->generate($this->createRouteName($admin, $action), $params);
    }

    /**
     * Check if a crud action exists for an entity.
     *
     * @param string|object $entity
     * @param string $action
     *
     * @return string
     */
    public function routeExists($entity, $action)
    {
        $admin = is_string($entity) ?
               $this->adminRegistry->getAdmin($entity) :
               $this->adminRegistry->getAdminForEntity($entity);

        return in_array($action, $admin->getActions());
    }

    protected function createRouteName(AdminInterface $admin, $action)
    {
        return $admin->getRoutePrefix().strtolower(preg_replace('/([A-Z])/', '_\1', $action));
    }
}
