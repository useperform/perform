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

    public function generate($crudName, $context, array $params = [])
    {
        $crudName = (string) $crudName;

        if(in_array($context, ['view', 'edit'])) {
            if (!isset($params['entity'])) {
                throw new \InvalidArgumentException(sprintf('Missing required "entity" parameter to generate a crud route for "%s", context "%s".', $crudName, $context));
            }

            $params = array_merge($params, ['id' => $params['entity']->getId()]);
            unset($params['entity']);
        }
        $crud = $this->crudRegistry->get($crudName);

        return $this->router->generate($this->createRouteName($crud, $context), $params);
    }

    /**
     * @return bool
     */
    public function routeExists($crudName, $context)
    {
        try {
            $crud = $this->crudRegistry->get($crudName);
        } catch (CrudNotFoundException $e) {
            return false;
        }

        if (!in_array($context, $crud->getActions())) {
            return false;
        }

        // getting the whole route collection is slow, will load routing files again
        return $this->router->getRouteCollection()->get($this->createRouteName($crud, $context)) instanceof Route;
    }

    protected function createRouteName(CrudInterface $crud, $action)
    {
        return $crud->getRoutePrefix().strtolower(preg_replace('/([A-Z])/', '_\1', $action));
    }

    public function generateDefaultEntityRoute($crudName)
    {
        return $this->router->generate($this->getDefaultEntityRoute($crudName));
    }

    public function getDefaultEntityRoute($crudName)
    {
        $crud = $this->crudRegistry->get($crudName);

        $actions = $crud->getActions();

        if (in_array('list', $actions)) {
            return $crud->getRoutePrefix().'list';
        }

        throw new \Exception(sprintf('Unable to find the default crud route for "%s".', $crudName));
    }
}
