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
     * Generate the URL for a crud context.
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
     * Check if a crud route exists.
     *
     * @param string $crudName
     * @param string $context
     *
     * @return bool
     */
    public function routeExists($crudName, $context);

    /**
     * Get the name of a crud route.
     *
     * @return bool
     *
     * @return string
     */
    public function getRouteName($crudName, $context);
}
