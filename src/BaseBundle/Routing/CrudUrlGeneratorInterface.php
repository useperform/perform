<?php

namespace Perform\BaseBundle\Routing;

/**
 * Generate URLs to different CRUD routes for entities, e.g. the list
 * context of a 'Bike' entity, or the view context of a 'Car' entity.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface CrudUrlGeneratorInterface
{
    /**
     * Get the url to a crud route for an entity.
     *
     * For the view and edit contexts, the 'entity' parameter is required.
     *
     * @param string $crudName
     * @param string $context
     * @param array  $params
     *
     * @return string
     */
    public function generate($crudName, $context, array $params = []);

    /**
     * Return true if the given crud name has at least one route registered.
     *
     * @return bool
     */
    public function isRouted($crudName);

    /**
     * Check if a crud route exists.
     *
     * @param string $crudName
     * @param string $context
     *
     * @return bool
     */
    public function routeExists($crudName, $context);

    /**
     * Get the name of the default CRUD route for an entity.
     *
     * @param string|object $entity
     */
    public function getDefaultEntityRoute($entity);

    /**
     * Generate the URL of the default CRUD route for an entity.
     *
     * @param string|object $entity
     */
    public function generateDefaultEntityRoute($entity);
}
