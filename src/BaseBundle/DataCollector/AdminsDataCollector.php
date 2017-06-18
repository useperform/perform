<?php

namespace Perform\BaseBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * AdminsDataCollector.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminsDataCollector extends DataCollector
{
    protected $registry;
    protected $store;
    protected $extendedEntities;

    public function __construct(AdminRegistry $registry, ConfigStoreInterface $store, array $extendedEntities)
    {
        $this->registry = $registry;
        $this->store = $store;
        $this->extendedEntities = $extendedEntities;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $admins = $this->registry->getAdmins();

        ksort($admins);
        $this->data = [
            'admins' => $admins,
            'extendedEntities' => $this->extendedEntities,
        ];
        if ($request->attributes->has('_entity')) {
            $this->data['activeEntity'] = $request->attributes->get('_entity');
            $this->data['activeAdmin'] = get_class($this->registry->getAdmin($this->data['activeEntity']));
            $this->data['typeConfig'] = $this->store->getTypeConfig($this->data['activeEntity'])->getAllTypes();
            $this->sanitize($this->data['typeConfig']);
            $this->data['addedConfigs'] = $this->store->getTypeConfig($this->data['activeEntity'])->getAddedConfigs();
            $this->sanitize($this->data['addedConfigs']);
        }
    }

    protected function sanitize(&$array)
    {
        array_walk_recursive($array, function (&$value) {
            if ($value instanceof \Closure) {
                $value = '(function)';
            }
        });
    }

    public function getAdmins()
    {
        return $this->data['admins'];
    }

    public function getActiveEntity()
    {
        return isset($this->data['activeEntity']) ? $this->data['activeEntity'] : null;
    }

    public function getActiveAdmin()
    {
        return isset($this->data['activeAdmin']) ? $this->data['activeAdmin'] : null;
    }

    public function getTypeConfig()
    {
        return isset($this->data['typeConfig']) ? $this->data['typeConfig'] : [];
    }

    public function getAddedConfigs()
    {
        return isset($this->data['addedConfigs']) ? $this->data['addedConfigs'] : [];
    }

    public function getExtendedEntities()
    {
        return $this->data['extendedEntities'];
    }

    public function getName()
    {
        return 'perform_base.admins';
    }
}
