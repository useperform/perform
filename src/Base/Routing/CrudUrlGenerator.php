<?php

namespace Admin\Base\Routing;

use Admin\Base\Admin\AdminRegistry;
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
     * @param mixed $entity
     * @param string $action
     *
     * @return string
     */
    public function generate($entity, $action)
    {
        $params = $action === 'list' ? [] : ['id' => $entity->getId()];
        $prefix = rtrim($this->adminRegistry->getAdminForEntity($entity)->getRoutePrefix(), '_');

        return $this->urlGenerator->generate($prefix.'_'.$action, $params);
    }
}
