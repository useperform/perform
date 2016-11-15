<?php

namespace Perform\BaseBundle\Routing;

use Perform\BaseBundle\Admin\AdminRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $params = $action === 'list' ? $params : array_merge($params, ['id' => $entity->getId()]);
        $admin = is_string($entity) ?
               $this->adminRegistry->getAdmin($entity) :
               $this->adminRegistry->getAdminForEntity($entity);
        $prefix = rtrim($admin->getRoutePrefix(), '_');

        return $this->urlGenerator->generate($prefix.'_'.$action, $params);
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
}
