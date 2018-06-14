<?php

namespace Perform\BaseBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Util\StringUtil;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudDataCollector extends DataCollector
{
    protected $registry;
    protected $store;
    protected $extendedEntities;

    public function __construct(CrudRegistry $registry, ConfigStoreInterface $store, array $extendedEntities)
    {
        $this->registry = $registry;
        $this->store = $store;
        $this->extendedEntities = $extendedEntities;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $crudNames = [];
        foreach ($this->registry->all() as $crudName => $crud) {
            $crudNames[$crudName] = [
                $crudClass = get_class($crud),
                $crudClass::getEntityClass(),
            ];
        }

        ksort($crudNames);
        $this->data = [
            'crudNames' => $crudNames,
            'extendedEntities' => $this->extendedEntities,
        ];
        if ($request->attributes->has('_crud')) {
            $crudName = $request->attributes->get('_crud');
            $this->data['activeCrud'] = $crudNames[$crudName][0];
            $this->data['activeCrudAlias'] = StringUtil::classBasename($crudNames[$crudName][0]);
            $this->data['activeEntity'] = $crudNames[$crudName][1];
            $this->data['fieldConfig'] = $this->cloneVar($this->store->getFieldConfig($crudName)->getAllTypes());
            $this->data['addedConfigs'] = $this->cloneVar($this->store->getFieldConfig($crudName)->getAddedConfigs());
        }
    }

    public function getCrudNames()
    {
        return $this->data['crudNames'];
    }

    public function getActiveCrud()
    {
        return isset($this->data['activeCrud']) ? $this->data['activeCrud'] : null;
    }

    public function getActiveCrudAlias()
    {
        return isset($this->data['activeCrudAlias']) ? $this->data['activeCrudAlias'] : null;
    }

    public function getActiveEntity()
    {
        return isset($this->data['activeEntity']) ? $this->data['activeEntity'] : null;
    }

    public function getFieldConfig()
    {
        return isset($this->data['fieldConfig']) ? $this->data['fieldConfig'] : [];
    }

    public function getAddedConfigs()
    {
        return isset($this->data['addedConfigs']) ? $this->data['addedConfigs'] : [];
    }

    public function getExtendedEntities()
    {
        return $this->data['extendedEntities'];
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getName()
    {
        return 'perform_base.crud';
    }
}
