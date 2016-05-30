<?php

namespace Admin\Base\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Admin\Base\Admin\AdminRegistry;

/**
 * AdminsDataCollector.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminsDataCollector extends DataCollector
{
    protected $registry;
    protected $extendedEntities;

    public function __construct(AdminRegistry $registry, array $extendedEntities)
    {
        $this->registry = $registry;
        $this->extendedEntities = $extendedEntities;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $admins = $this->registry->getAdmins();
        foreach ($admins as $entity => $class) {
            if (strpos($entity, '\\') !== false) {
                unset($admins[$entity]);
            }
        }

        ksort($admins);
        $this->data = [
            'admins' => $admins,
            'extendedEntities' => $this->extendedEntities,
        ];
        if ($request->attributes->has('_entity')) {
            $this->data['activeEntity'] = $request->attributes->get('_entity');
            $this->data['activeAdmin'] = get_class($this->registry->getAdmin($this->data['activeEntity']));
        }
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

    public function getExtendedEntities()
    {
        return $this->data['extendedEntities'];
    }

    public function getName()
    {
        return 'admin_base.admins';
    }
}
